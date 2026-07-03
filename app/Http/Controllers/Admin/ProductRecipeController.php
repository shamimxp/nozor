<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterialProduct;
use Illuminate\Http\Request;
use Ramsey\Collection\Queue;

class ProductRecipeController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = \App\Models\ProductRecipe::with('product')->latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    return $row->product ? $row->product->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('admin.product-recipe.edit', $row->id).'" class="btn btn-primary p-0 px-25 editBtn" title="Edit"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger p-0 px-25 deleteBtn" title="Delete"><i data-feather="trash-2"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.product-recipe.index');
    }
    public function create(){
        $products = Product::where('status',1)->get();
        $raw_material = RawMaterialProduct::all();
        return view('admin.product-recipe.create', compact('products', 'raw_material'));
    }
    public function store(Request $request){

    }
    public function show(){
        return view('admin.product-recipe.show');
    }
    public function edit(){
        return view('admin.product-recipe.edit');
    }
    public function update(Request $request){

    }

}
