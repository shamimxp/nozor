<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    use HandlesTrash;

    protected function trashModuleKey(): string
    {
        return 'product_review';
    }

    public function index()
    {
        $reviews = ProductReview::with('product')->latest()->get();
        return view('admin.product_review.index', compact('reviews'));
    }

    public function updateStatus(Request $request)
    {
        $review = ProductReview::findOrFail($request->id);
        $review->status = $request->status;
        $review->save();

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['reply' => 'nullable|string']);
        $review = ProductReview::findOrFail($id);
        $review->reply = $request->reply;
        $review->save();

        return redirect()->back()->with('success', 'Reply updated successfully.');
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }
}
