<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOffer;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;

class ProductOfferController extends Controller
{
    use HandlesTrash, AuthorizesAdminModule;

    protected function trashModuleKey(): string
    {
        return 'product_offer';
    }

    public function __construct()
    {
        $this->authorizeAdminModule('product_offer', [
            'product_offer.edit' => ['getStatus'],
        ]);
    }

    public function index()
    {
        $offers = ProductOffer::with(['product:id,name', 'createdBy:id,name'])
            ->latest()
            ->get();

        return view('admin.product_offer.index', compact('offers'));
    }

    public function create()
    {
        $products = Product::where('status', 1)->orderBy('name')->get(['id', 'name']);

        return view('admin.product_offer.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        ProductOffer::create(array_merge($validated, [
            'created_by' => auth('admin')->id(),
        ]));

        toastr()->success('Product offer created successfully.');

        return redirect()->route('admin.product-offer.index');
    }

    public function edit($id)
    {
        $offer = ProductOffer::findOrFail($id);
        $products = Product::where('status', 1)
            ->orWhere('id', $offer->product_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.product_offer.edit', compact('offer', 'products'));
    }

    public function update(Request $request, $id)
    {
        $offer = ProductOffer::findOrFail($id);
        $validated = $this->validatedData($request);
        $offer->update($validated);

        toastr()->success('Product offer updated successfully.');

        return redirect()->route('admin.product-offer.index');
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }

    public function getStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:product_offers,id',
            'status' => 'required|in:0,1',
        ]);

        $offer = ProductOffer::findOrFail($request->id);
        $offer->status = (int) $request->status;
        $offer->save();

        return response()->json([
            'success' => 'Status changed successfully.',
            'status' => $offer->status,
        ]);
    }

    protected function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'from_qty' => 'required|integer|min:1',
            'to_qty' => 'required|integer|gte:from_qty',
            'offer_type' => 'required|in:fixed,percent',
            'discount_value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('offer_type') === 'percent' && (float) $value > 100) {
                        $fail('Percentage discount cannot be greater than 100.');
                    }
                },
            ],
            'status' => 'required|in:0,1',
        ]);

        $validated['status'] = (int) $validated['status'];

        return $validated;
    }
}
