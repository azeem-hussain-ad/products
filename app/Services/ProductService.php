<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function createProduct($name, $sku, $price, $currency, $variations, $quantity, $status)
    {
        $product = new Product();
        $product->name = $name;
        $product->sku = isset($sku) ? $sku : null;
        $product->price = $price;
        $product->currency = $currency;
        $product->variations = $variations;
        $product->quantity = $quantity;
        $product->status = $status;

        $product->save();

        return $product;
    }

    public function findProductByName($name)
    {
        return Product::where('name', $name)->first();
    }

    public static function findProductById($id)
    {
        return Product::where('id', $id)->first();
    }

    public function updateProductQuantity($productId, $newQuantity)
    {
        $product = self::findProductById($productId);

        if ($product) {
            $product->update([
                'quantity' => $product->quantity+$newQuantity,
            ]);

            return $product;
        }

        return null;
    }

}

