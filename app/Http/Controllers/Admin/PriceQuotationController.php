<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\WebSetting;
use App\Services\ProductPriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class PriceQuotationController extends Controller
{
    use AuthorizesAdminModule;

    public const PRICE_TYPES = [
        'dealer' => 'Dealer Price',
        'special_dealer' => 'Special Dealer Price',
        'customer' => 'Customer Price',
    ];

    public function __construct()
    {
        $this->authorizeAdminModule('price_quotation', [
            'price_quotation.list' => ['index', 'products'],
            'price_quotation.create' => ['pdf'],
        ]);
    }

    public function index()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get(['id', 'name']);
        $priceTypes = self::PRICE_TYPES;
        $company = $this->companyMeta();

        return view('admin.price_quotation.index', compact('categories', 'priceTypes', 'company'));
    }

    public function products(Request $request, ProductPriceCalculator $calculator)
    {
        $request->validate([
            'price_type' => 'required|in:dealer,special_dealer,customer',
            'category_id' => 'nullable|integer',
            'search' => 'nullable|string|max:120',
            'only_manufacturer' => 'nullable|boolean',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:8|max:60',
        ]);

        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 24);

        $query = Product::query()
            ->with(['category:id,name', 'recipe:id,product_id,manufacture_images'])
            ->where('status', 1)
            ->where(function ($q) {
                // Regular products must already have a positive selling price.
                // Manufacture products are price-checked after quote calculation.
                $q->where(function ($inner) {
                    $inner->where('is_manufacturer', 0)
                        ->whereNotNull('selling_price')
                        ->where('selling_price', '>', 0);
                })->orWhere('is_manufacturer', 1);
            })
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('only_manufacturer')) {
            $query->where('is_manufacturer', 1);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model_no', 'like', "%{$search}%");
            });
        }

        $items = $query->get()
            ->map(function (Product $product) use ($calculator, $request) {
                return $this->presentProduct($product, $request->price_type, $calculator);
            })
            ->filter(function (array $item) {
                return $item['final_price'] !== null && (float) $item['final_price'] > 0;
            })
            ->values();

        $total = $items->count();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();
        $hasMore = ($page * $perPage) < $total;

        return response()->json([
            'success' => true,
            'data' => $slice,
            'count' => $slice->count(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'has_more' => $hasMore,
        ]);
    }

    public function pdf(Request $request, ProductPriceCalculator $calculator)
    {
        $validated = $request->validate([
            'price_type' => 'required|in:dealer,special_dealer,customer',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'title' => 'nullable|string|max:120',
            'note' => 'nullable|string|max:500',
        ]);

        $products = Product::with(['category:id,name', 'recipe:id,product_id,manufacture_images'])
            ->whereIn('id', $validated['product_ids'])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'No valid products selected.');
        }

        $rows = $products->map(fn (Product $product) => $this->presentProduct(
            $product,
            $validated['price_type'],
            $calculator
        ))->filter(function (array $item) {
            return $item['final_price'] !== null && (float) $item['final_price'] > 0;
        })->values();

        if ($rows->isEmpty()) {
            return back()->with('error', 'Selected products have no valid price for this type.');
        }

        $grouped = $rows->groupBy(fn ($row) => $row['category_name'] ?: 'Uncategorized');
        $company = $this->companyMeta();
        $priceType = $validated['price_type'];
        $priceTypeLabel = self::PRICE_TYPES[$priceType];
        $generatedAt = now();
        $title = $validated['title'] ?: ($priceTypeLabel . ' Quotation');
        $note = $validated['note'] ?? null;

        $filename = 'price-quotation-' . $priceType . '-' . $generatedAt->format('Ymd-His') . '.pdf';

        $html = view('admin.price_quotation.pdf', compact(
            'grouped',
            'rows',
            'company',
            'priceType',
            'priceTypeLabel',
            'generatedAt',
            'title',
            'note'
        ))->render();

        $pdfBinary = $this->renderPdfWithChrome($html);

        return response()->streamDownload(function () use ($pdfBinary) {
            echo $pdfBinary;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Render HTML to PDF via Chrome headless (works without PHP GD).
     */
    protected function renderPdfWithChrome(string $html): string
    {
        $dir = storage_path('app/tmp/price-quotations');
        File::ensureDirectoryExists($dir);

        $id = uniqid('pq_', true);
        $htmlPath = $dir . DIRECTORY_SEPARATOR . $id . '.html';
        $pdfPath = $dir . DIRECTORY_SEPARATOR . $id . '.pdf';

        File::put($htmlPath, $html);

        try {
            $chrome = $this->resolveChromeBinary();
            $process = new Process([
                $chrome,
                '--headless=new',
                '--disable-gpu',
                '--no-pdf-header-footer',
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--allow-file-access-from-files',
                '--print-to-pdf=' . $pdfPath,
                'file://' . $htmlPath,
            ]);
            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful() || !is_file($pdfPath) || filesize($pdfPath) < 100) {
                throw new \RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput()) ?: 'Chrome PDF generation failed.');
            }

            return File::get($pdfPath);
        } finally {
            @unlink($htmlPath);
            @unlink($pdfPath);
        }
    }

    protected function resolveChromeBinary(): string
    {
        $candidates = [
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
            'google-chrome',
            'google-chrome-stable',
            'chromium-browser',
            'chromium',
        ];

        foreach ($candidates as $bin) {
            if (str_contains($bin, '/')) {
                if (is_executable($bin)) {
                    return $bin;
                }
                continue;
            }

            $process = new Process(['bash', '-lc', 'command -v ' . escapeshellarg($bin)]);
            $process->run();
            $path = trim($process->getOutput());
            if ($path !== '' && is_executable($path)) {
                return $path;
            }
        }

        throw new \RuntimeException('Chrome/Chromium is required to generate quotation PDFs.');
    }

    protected function presentProduct(Product $product, string $priceType, ProductPriceCalculator $calculator): array
    {
        $pricing = $this->resolvePricing($product, $calculator);
        $base = $this->pickBasePrice($pricing, $priceType);
        $discount = $this->discountMeta($product, $base);
        $final = $discount['final_price'] ?? $base;

        $imagePath = $this->productImagePath($product);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'model_no' => $product->model_no,
            'category_id' => $product->category_id,
            'category_name' => $product->category->name ?? null,
            'is_manufacturer' => (bool) $product->is_manufacturer,
            'image_url' => $this->productImageUrl($product),
            'image_path' => $imagePath,
            'pricing_type' => $pricing['type'],
            'pricing_available' => $pricing['available'],
            'base_price' => $base,
            'discount_type' => $discount['discount_type'],
            'discount_amount' => $discount['discount_amount'],
            'discount_label' => $discount['discount_label'],
            'discount_value' => $discount['discount_value'],
            'final_price' => $final,
            'has_discount' => $discount['has_discount'],
        ];
    }

    protected function resolvePricing(Product $product, ProductPriceCalculator $calculator): array
    {
        if ($product->is_manufacturer) {
            if (!$product->recipe) {
                return [
                    'type' => 'manufacture',
                    'available' => false,
                    'dealer' => null,
                    'special_dealer' => null,
                    'customer' => null,
                ];
            }

            try {
                $quote = $calculator->quote($product->id);
            } catch (\Throwable $e) {
                return [
                    'type' => 'manufacture',
                    'available' => false,
                    'dealer' => null,
                    'special_dealer' => null,
                    'customer' => null,
                ];
            }

            return [
                'type' => 'manufacture',
                'available' => true,
                'dealer' => isset($quote['regular_dealer_price']) ? (float) $quote['regular_dealer_price'] : null,
                'special_dealer' => isset($quote['special_dealer_price']) ? (float) $quote['special_dealer_price'] : null,
                'customer' => isset($quote['regular_price']) ? (float) $quote['regular_price'] : null,
            ];
        }

        $wholesale = $product->selling_price !== null ? (float) $product->selling_price : null;

        return [
            'type' => 'regular',
            'available' => $wholesale !== null,
            'dealer' => $wholesale,
            'special_dealer' => $wholesale,
            'customer' => $wholesale,
        ];
    }

    protected function pickBasePrice(array $pricing, string $priceType): ?float
    {
        if (!$pricing['available']) {
            return null;
        }

        $value = $pricing[$priceType] ?? null;

        return $value !== null ? (float) $value : null;
    }

    protected function discountMeta(Product $product, ?float $base): array
    {
        $empty = [
            'has_discount' => false,
            'discount_type' => null,
            'discount_amount' => null,
            'discount_label' => null,
            'discount_value' => null,
            'final_price' => $base,
        ];

        if ($base === null || !$product->discount_type || $product->discount_amount === null || (float) $product->discount_amount <= 0) {
            return $empty;
        }

        $amount = (float) $product->discount_amount;
        $type = $product->discount_type;

        if ($type === 'percent') {
            $discountValue = round($base * ($amount / 100), 2);
            $final = round(max(0, $base - $discountValue), 2);
            $label = rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.') . '% off';
        } elseif ($type === 'amount') {
            $discountValue = round(min($base, $amount), 2);
            $final = round(max(0, $base - $discountValue), 2);
            $label = '৳' . number_format($discountValue, 2) . ' off';
        } else {
            return $empty;
        }

        return [
            'has_discount' => $discountValue > 0,
            'discount_type' => $type,
            'discount_amount' => $amount,
            'discount_label' => $label,
            'discount_value' => $discountValue,
            'final_price' => $final,
        ];
    }

    protected function productImagePath(Product $product): ?string
    {
        $candidates = [];

        $recipeImages = $product->recipe->manufacture_images ?? null;
        if (is_array($recipeImages) && !empty($recipeImages[0])) {
            $path = ltrim((string) $recipeImages[0], '/');
            $candidates[] = public_path($path);
            $candidates[] = public_path('storage/' . $path);
            $candidates[] = storage_path('app/public/' . $path);
        }

        if ($product->featured_image) {
            $file = ltrim($product->featured_image, '/');
            $candidates[] = public_path(trim(config('imagepath.product'), '/') . '/' . $file);
            $candidates[] = public_path($file);
        }

        $candidates[] = public_path('images/no-image.png');

        foreach ($candidates as $candidate) {
            if ($candidate && is_file($candidate)) {
                return realpath($candidate) ?: $candidate;
            }
        }

        return null;
    }

    protected function productImageUrl(Product $product): string
    {
        $path = $this->productImagePath($product);
        if (!$path) {
            return asset('images/no-image.png');
        }

        $public = realpath(public_path()) ?: public_path();
        if (str_starts_with($path, $public)) {
            $relative = ltrim(str_replace('\\', '/', substr($path, strlen($public))), '/');
            return asset($relative);
        }

        return asset('images/no-image.png');
    }

    protected function companyMeta(): array
    {
        $setting = WebSetting::first();
        $logoCandidates = [
            public_path('images/nozor_logo.png'),
            public_path('admin/app-assets/images/logo/logo.png'),
            public_path('admin/app-assets/images/logo/edited_red_letters.svg'),
        ];

        $logoPath = null;
        foreach ($logoCandidates as $candidate) {
            if (is_file($candidate) && !str_ends_with(strtolower($candidate), '.svg')) {
                $logoPath = $candidate;
                break;
            }
        }

        return [
            'name' => config('app.name', 'WOOD Machinery'),
            'phone' => $setting->contact_number_1 ?? null,
            'phone2' => $setting->contact_number_2 ?? null,
            'email' => $setting->email ?? null,
            'address' => $setting->address ?? null,
            'logo_path' => $logoPath,
            'logo_url' => $logoPath ? asset(str_replace(public_path() . DIRECTORY_SEPARATOR, '', $logoPath)) : asset('images/no-image.png'),
            'currency' => $setting->currency_symbol ?? '৳',
        ];
    }
}
