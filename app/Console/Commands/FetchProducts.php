<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fetch the products every 12am from https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get('https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products');

        if ($response->successful()) {
            $products = $response->json();

            foreach ($products as $product) {
                $existingProduct = Product::find($product['id']);

                if ($existingProduct) {
                    // Update the price of the existing product
                    $existingProduct->update([
                        'price' => $product['price'],
                        'quantity' => 15,
                    ]);
                } else {
                    // Insert a new product
                    Product::create([
                        'id' => $product['id'], // Assuming 'id' is the primary key
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'status' => 'sale',
                        'quantity' => 5,
                    ]);
                }
            }

            $this->info('Products fetched and inserted successfully.');
        } else {
            $this->error('Failed to fetch products from the API.');
        }
    }
}
