<?php

namespace App\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Output\OutputInterface;

class GetCategoryPricesCommand extends Command
{
    protected $signature = 'get:category-prices';
    protected $description = 'Get the total prices of products by category';

    public function handle(Command $command): void
    {
        $response = Http::get('https://fakestoreapi.com/products');

        if ($response->failed()) {
            $this->error('Failed to fetch product data from the API.');
        }
        else
        {
            $products = $response->json();
            $categoriesTotalPrice = [];

            foreach ($products as $product) {

                $category = $product['category'];
                $price = $product['price'];

                if (isset($categoriesTotalPrice[$category])) {
                    $categoriesTotalPrice[$category] += $price;
                }
                else
                {
                    $categoriesTotalPrice[$category] = $price;
                }
            }

            foreach ($categoriesTotalPrice as $category => $total)
            {
                echo "$category - $total" . PHP_EOL;
            }
        }

    }

}
