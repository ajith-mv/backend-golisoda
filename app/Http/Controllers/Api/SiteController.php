<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteResource;
use App\Models\GlobalSettings;

class SiteController extends Controller
{
    public function siteInfo()
    {
        try {
            $siteDetails = GlobalSettings::first();
           
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound();
        }
        
        return new SiteResource($siteDetails);
        // return true;
    }
}
