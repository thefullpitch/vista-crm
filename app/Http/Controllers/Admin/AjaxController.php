<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;

class AjaxController extends Controller
{
    public function getStates(Request $request)
    {
        $states = \App\Models\State::where('zone_id', $request->zone_id)->where('status', 1)->get();
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->where('status', 1)->get();
        return response()->json($cities);
    }

    public function getPincodes(Request $request)
    {
        $pincodes = Pincode::where('city_id', $request->city_id)->where('status', 1)->get();
        return response()->json($pincodes);
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = \App\Models\Subcategory::where('category_id', $request->category_id)->where('status', 'Active')->get();
        return response()->json($subcategories);
    }

    public function getShopsByPincode(Request $request)
    {
        $query = \App\Models\Shop::where('status', 'Active')->with('owner', 'pincode');
        if ($request->pincode_id && $request->all != 'true') {
            $query->where('pincode_id', $request->pincode_id);
        }
        $shops = $query->get();
        return response()->json($shops);
    }
}
