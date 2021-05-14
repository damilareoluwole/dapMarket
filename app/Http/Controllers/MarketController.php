<?php

namespace App\Http\Controllers;

use App\Classes\General;
//use App\Jobs\LogGatewayRequestsToFile;
//use App\Models\GatewayRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarketController extends Controller
{

    public function category(Request $request)
    {
        $categories = getCategories();
        return $categories;
    }

}


