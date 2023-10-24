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
            // notify to the warehouse
            $warehouseName = env('WAREHOUSE_NAME');
            $warehouseEmail = env('WAREHOUSE_EMAIL');

            echo "email sent to warehouse named -> " .$warehouseName . " with email-> " .$warehouseEmail."; ";

            if($product->quantity > 0 && $product->quantity != 'NULL'){

                return response()->json(['message' => 'Product quantity updated ' , 'quantity'=> $updatedProduct->quantity], 200);
            }

            $notifieables = NotifyUsersController::getNotieables($id);

            foreach ($notifieables as $notifyable) {
                // send the nofication to user

                echo "email sent to user -> " .$notifyable->user->name ."; ";
            }

            // update the nofiable users to true means they are notified
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
        $csvProductIds =[];

        if (file_exists($csvFile)) {
            $file = fopen($csvFile, 'r');
            $header = fgetcsv($file);

            while ($row = fgetcsv($file)) {
                $id = $row[0];
                $name = $row[1];
                $sku = $row[2];
                $price = $row[3];
                $currency = $row[4];
                $variations = $row[5];
                $quantity = $row[6];
                $status = $row[7];

                if (isset($name) && isset($id)) {
                    $checkProductByid = $this->productService->findProductById($id);
                    $csvProductIds[] = $id;

                    if (!$checkProductByid) {
                        $product = $this->productService->createProduct($id, $name, $sku, $price, $currency, $variations, $quantity, $status);
                    }
                }

            }

            fclose($file);
            $allProductIds = $this->productService->getAllProductsIds();

            // Filter out the product IDs added during the current import
            $addedProductIds = array_diff($allProductIds, $csvProductIds);

            $softDel = $this->productService->softDeleteProduct($addedProductIds);
            return response()->json(['message' => 'Products imported successfully'], 200);
        } else {
            return response()->json(['message' => 'CSV file not found'], 404);
        }
    }
}
