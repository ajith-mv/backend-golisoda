<?php

namespace Database\Seeders;

use App\Models\GlobalSiteLinks;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ins['site_id']     = 1;
        $ins['link_name']   = 'facebook';
        $ins['link_icon']   = 'facebook';
        $ins['link_url']    = 'https://www.facebook.com/gbssystems/';
        $ins['status']      = 'published';
        GlobalSiteLinks::updateOrCreate(['link_icon' => 'facebook'], $ins);
        $ins = [];
        $ins['site_id']     = 1;
        $ins['link_name']   = 'twitter';
        $ins['link_icon']   = 'twitter';
        $ins['link_url']    = 'https://twitter.com/gbs_systems';
        $ins['status']      = 'published';
        GlobalSiteLinks::updateOrCreate(['link_icon' => 'twitter'], $ins);

        $ins = [];
        $ins['site_id']     = 1;
        $ins['link_name']   = 'youtube';
        $ins['link_icon']   = 'youtube';
        $ins['link_url']    = 'https://www.youtube.com/c/GBSSystems';
        $ins['status']      = 'published';
        GlobalSiteLinks::updateOrCreate(['link_icon' => 'youtube'], $ins);

        $ins = [];
        $ins['site_id']     = 1;
        $ins['link_name']   = 'instagram';
        $ins['link_icon']   = 'instagram';
        $ins['link_url']    = 'https://www.instagram.com/gbs_systems/';
        $ins['status']      = 'published';
        GlobalSiteLinks::updateOrCreate(['link_icon' => 'instagram'], $ins);

        $ins = [];
        $ins['site_id']     = 1;
        $ins['link_name']   = 'linkedin';
        $ins['link_icon']   = 'linkedin';
        $ins['link_url']    = 'https://www.linkedin.com/company/gbssystems/?originalSubdomain=in';
        $ins['status']      = 'published';
        GlobalSiteLinks::updateOrCreate(['link_icon' => 'linkedin'], $ins);

    }
}
