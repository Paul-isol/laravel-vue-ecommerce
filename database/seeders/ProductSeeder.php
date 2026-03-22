<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'title' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'quantity' => 10,
                'description' => 'The latest iPhone with the best camera and performance',
                'price' => 1099.99,
                'category_id' => 1,
                'brand_id' => 1,
            ],
            [
                'title' => 'Samsung Galaxy S23 Ultra',
                'slug' => 'samsung-galaxy-s23-ultra',
                'description' => 'The latest Samsung phone with the best camera and performance',
                'price' => 1199.99,
                'quantity' => 10,
                'category_id' => 1,
                'brand_id' => 2,
            ],
            [
                'title' => 'Xiaomi 13 Pro',
                'slug' => 'xiaomi-13-pro',
                'description' => 'The latest Xiaomi phone with the best camera and performance',
                'price' => 999.99,
                'quantity' => 10,
                'category_id' => 1,
                'brand_id' => 3,
            ],
            [
                'title' => 'Huawei P60 Pro',
                'slug' => 'huawei-p60-pro',
                'description' => 'The latest Huawei phone with the best camera and performance',
                'price' => 899.99,
                'quantity' => 10,
                'category_id' => 1,
                'brand_id' => 4,
            ],
            [
                'title' => 'OnePlus 11',
                'slug' => 'oneplus-11',
                'description' => 'The latest OnePlus phone with the best camera and performance',
                'price' => 799.99,
                'quantity' => 10,
                'category_id' => 1,
                'brand_id' => 5,
            ],
        ];
        foreach($products as $product) {
            Product::create([
                'title' => $product['title'],
                'slug' => $product['slug'],
                'description' => $product['description'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'category_id' => $product['category_id'],
                'brand_id' => $product['brand_id'],
                'published' => 1,
                'inStock' => 1,
            ]);
        }
    }
}
