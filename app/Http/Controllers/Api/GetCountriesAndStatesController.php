<?php

namespace App\Http\Controllers\Api;
use App\Models\Country;
use App\Models\State;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetCountriesAndStatesController extends Controller
{
     
    public function getCountries()
    {
        return response()->json(Country::all());
    }


    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)->get();
        return response()->json($states);
    }

}
