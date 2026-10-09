<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\ProductRecipeItem;
use Illuminate\Http\Request;

class ProductRecipeWriter
{
    public function sync(Product $product, Request $request, ?ProductRecipe $recipe = null): ProductRecipe
    {
        $recipe = $recipe ?: ProductRecipe::firstOrNew(['product_id' => $product->id]);

        $oldImages = is_array($recipe->manufacture_images) ? $recipe->manufacture_images : [];
        $existingImages = $request->input('existing_images', []);
        if (!is_array($existingImages)) {
            $existingImages = [];
        }

        foreach ($oldImages as $oldImage) {
            if (!in_array($oldImage, $existingImages, true) && file_exists(public_path($oldImage))) {
                @unlink(public_path($oldImage));
            }
        }

        $manufactureImages = array_values($existingImages);
        if ($request->hasFile('manufacture_images')) {
            foreach ($request->file('manufacture_images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/product_recipes'), $filename);
                $manufactureImages[] = 'uploads/product_recipes/' . $filename;
            }
        }

        $recipe->fill([
            'product_id' => $product->id,
            'body_charge' => $request->filled('body_charge') ? $request->body_charge : 0,
            'finishing_charge' => $request->filled('finishing_charge') ? $request->finishing_charge : 0,
            'bearing' => $request->filled('bearing') ? $request->bearing : 0,
            'stone' => $request->filled('stone') ? $request->stone : 0,
            'electric_bill' => $request->filled('electric_bill') ? $request->electric_bill : 0,
            'gas_bill' => $request->filled('gas_bill') ? $request->gas_bill : 0,
            'box_price' => $request->filled('box_price') ? $request->box_price : 0,
            'carrying_charge' => $request->filled('carrying_charge') ? $request->carrying_charge : 0,
            'wire_price' => $request->filled('wire_price') ? $request->wire_price : 0,
            'manufacture_images' => $manufactureImages,
        ]);
        $recipe->save();

        $recipe->items()->delete();

        $rawMaterialIds = $request->input('raw_material_id', []);
        if (is_array($rawMaterialIds)) {
            foreach ($rawMaterialIds as $key => $rawMaterialId) {
                if (!$rawMaterialId) {
                    continue;
                }

                ProductRecipeItem::create([
                    'product_recipe_id' => $recipe->id,
                    'raw_material_product_id' => $rawMaterialId,
                    'thickness' => $request->input('thickness.' . $key, 0) ?? 0,
                    'width' => $request->input('width.' . $key, 0) ?? 0,
                    'height' => $request->input('height.' . $key, 0) ?? 0,
                    'area' => $request->input('area.' . $key, 0) ?? 0,
                    'grade_value' => $request->input('grade_value.' . $key, 0) ?? 0,
                    'qty' => $request->input('qty.' . $key, 0) ?? 0,
                ]);
            }
        }

        return $recipe;
    }
}
