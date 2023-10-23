<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\NotifyUsersController;
use App\Notifications\QuantityUpdateNotification;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function updateQuantity($id, $quantity)
    {

        $product = $this->productService->findProductById($id);

        if ($product) {
            $updatedProduct =  $this->productService->updateProductQuantity($id, $quantity);

            if($product->quantity > 0 && $product->quantity != 'NULL'){

                return response()->json(['message' => 'Product quantity updated ' , 'quantity'=> $updatedProduct->quantity], 200);
            }

            $notifieables = NotifyUsersController::getNotieables($id);

            foreach ($notifieables as $notifyable) {
                // send the nofication to user

                echo "email sent to user:-->" .$notifyable->user->name;
            }

            NotifyUsersController::updateNotifieables($id);

            return response()->json(['message' => 'Product Quantity updated ', 'quantity'=> $updatedProduct->quantity], 200);

        } else {
            return response()->json(['message' => 'Product no found'], 404);
        }
    }

    public function checkProduct($id, $userId)
    {
        $product = $this->productService->findProductById($id);

        if ($product) {
            if($product->quantity > 0 && $product->quantity != 'NULL'){
                return response()->json(['message' => 'Product found go ahead and shop quantity:'. $product->quantity], 200);
            }

            $notification = NotifyUsersController::checkNotifiable($userId, $id);
            if (count($notification) <= 0) {
                NotifyUsersController::addNotiyUser($userId, $id);
            }
            return response()->json(['message' => 'Out of Stock will notify later!',], 200);

        } else {
            return response()->json(['message' => 'Product no found'], 404);
        }
    }

    public function importProducts() {
        $csvFile = base_path('database/seeders/csv/products.csv'); // Path to your CSV file

        if (file_exists($csvFile)) {
            $file = fopen($csvFile, 'r');
            $header = fgetcsv($file);

            while ($row = fgetcsv($file)) {
                $name = $row[1];
                $sku = $row[2];
                $price = $row[3];
                $currency = $row[4];
                $variations = $row[5];
                $quantity = $row[6];
                $status = $row[7];

                if (isset($name)) {

                    $product = $this->productService->createProduct($name, $sku, $price, $currency, $variations, $quantity, $status);
                }

            }

            fclose($file);

            return response()->json(['message' => 'Products imported successfully'], 200);
        } else {
            return response()->json(['message' => 'CSV file not found'], 404);
        }
    }
}
