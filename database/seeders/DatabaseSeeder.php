<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            SiteSeeder::class,
            CountrySeed::class,
            OrderStatusSeeder::class,
            EmailTemplateTypeSeeder::class,
            EmailTemplateSeeder::class,
            BrandSeeder::class,
            TaxSeeder::class,
            CategorySeeder::class,
            SocialLinkSeeder::class
            
        ]);
    }
}
