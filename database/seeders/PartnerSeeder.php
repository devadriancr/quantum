<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Partner::create(['code' => '200000', 'name' => 'MMVO', 'partner_type' =>'customer']);
        Partner::create(['code' => '400403', 'name' => 'TOYOTA', 'partner_type' =>'customer']);
        Partner::create(['code' => '200700', 'name' => 'MNAO', 'partner_type' =>'customer']);
        Partner::create(['code' => '400501', 'name' => 'F&P', 'partner_type' =>'customer']);
    }
}
