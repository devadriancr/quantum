<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemType::create(['code' => 'F', 'name' => 'FinishedProd']);
        ItemType::create(['code' => 'G', 'name' => 'OutsideProd']);
        ItemType::create(['code' => 'H', 'name' => 'Supply FYP']);
        ItemType::create(['code' => 'I', 'name' => 'Parts FYP']);
        ItemType::create(['code' => 'M', 'name' => 'Manufactured']);
        ItemType::create(['code' => 'P', 'name' => 'Purchased']);
        ItemType::create(['code' => 'S', 'name' => 'Supply']);
        ItemType::create(['code' => 'T', 'name' => 'Materials']);
        ItemType::create(['code' => '0', 'name' => 'Phanthom']);
        ItemType::create(['code' => '3', 'name' => 'Assorment']);
        ItemType::create(['code' => '4', 'name' => 'Kit']);
        ItemType::create(['code' => '5', 'name' => 'Planning Bill']);
        ItemType::create(['code' => '6', 'name' => 'NON-INV']);
    }
}
