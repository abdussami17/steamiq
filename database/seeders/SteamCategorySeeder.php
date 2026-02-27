<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SteamCategory; // your model

class SteamCategorySeeder extends Seeder
{
    public function run()
    {
        // Only insert if table is empty
        if (SteamCategory::count() == 0) {
            $categories = ['Science', 'Technology', 'Engineering', 'Art', 'Math'];

            foreach ($categories as $name) {
                SteamCategory::create([
                    'name' => $name,
                   
                ]);
            }
        }
    }
}