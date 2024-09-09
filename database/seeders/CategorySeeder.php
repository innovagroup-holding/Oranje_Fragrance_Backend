<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        Category::create(['name' => 'categorie1', 'image' => 'storage/images/categories/1.jfif']);
        Category::create(['name' => 'categorie2', 'image' => 'storage/images/categories/2.jfif']);
        Category::create(['name' => 'categorie3', 'image' => 'storage/images/categories/3.jfif']);
        Category::create(['name' => 'categorie4', 'image' => 'storage/images/categories/4.jfif']);
        Category::create(['name' => 'categorie5', 'image' => 'storage/images/categories/5.jfif']);
    }
}