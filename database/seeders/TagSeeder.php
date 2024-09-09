<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::create(['name' => 'For Her', 'image' => 'storage/images/tags/tag1.jfif']);
        Tag::create(['name' => 'For him', 'image' => 'storage/images/tags/tag2.jpg']);
        Tag::create(['name' => 'New', 'image' => 'storage/images/tags/tag3.jpg']);
        Tag::create(['name' => 'Popular', 'image' => 'storage/images/tags/tag4.jpg']);
        Tag::create(['name' => 'Discount', 'image' => '']);
    }
}