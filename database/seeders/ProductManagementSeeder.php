<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;

class ProductManagementSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::create(['name' => 'Electronics', 'status' => 'Active']);
        $clothing = Category::create(['name' => 'Clothing', 'status' => 'Active']);

        $mobiles = Subcategory::create(['category_id' => $electronics->id, 'name' => 'Mobile Phones', 'status' => 'Active']);
        $laptops = Subcategory::create(['category_id' => $electronics->id, 'name' => 'Laptops', 'status' => 'Active']);

        $menswear = Subcategory::create(['category_id' => $clothing->id, 'name' => 'Mens Wear', 'status' => 'Active']);
        $womenswear = Subcategory::create(['category_id' => $clothing->id, 'name' => 'Womens Wear', 'status' => 'Active']);

        Product::create(['category_id' => $electronics->id, 'subcategory_id' => $mobiles->id, 'name' => 'iPhone 15', 'price' => 799.99, 'status' => 'Active']);
        Product::create(['category_id' => $electronics->id, 'subcategory_id' => $mobiles->id, 'name' => 'Samsung Galaxy S24', 'price' => 849.99, 'status' => 'Active']);
        
        Product::create(['category_id' => $electronics->id, 'subcategory_id' => $laptops->id, 'name' => 'MacBook Air M2', 'price' => 1199.99, 'status' => 'Active']);
        Product::create(['category_id' => $electronics->id, 'subcategory_id' => $laptops->id, 'name' => 'Dell XPS 13', 'price' => 999.99, 'status' => 'Active']);

        Product::create(['category_id' => $clothing->id, 'subcategory_id' => $menswear->id, 'name' => 'Levis Denim Jacket', 'price' => 89.99, 'status' => 'Active']);
        Product::create(['category_id' => $clothing->id, 'subcategory_id' => $menswear->id, 'name' => 'Nike Running T-Shirt', 'price' => 34.99, 'status' => 'Active']);

        Product::create(['category_id' => $clothing->id, 'subcategory_id' => $womenswear->id, 'name' => 'Zara Summer Dress', 'price' => 59.99, 'status' => 'Active']);
    }
}
