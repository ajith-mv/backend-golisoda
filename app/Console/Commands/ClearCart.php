<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class ClearCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Cart';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('cart_product_variation_options')
            ->whereIn('cart_id', function($query) {
                $query->select('id')
                    ->from('carts')
                    ->whereNull('customer_id')
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') < CURDATE()");
            })
            ->delete();
        DB::table('carts')
                ->whereNull('customer_id')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') < CURDATE()")
                ->delete();
        \Log::info("Cron is working fine!");
    }
}
