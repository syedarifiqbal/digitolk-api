<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth()->user()->locations()->orderBy('id', 'desc')->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Locations $locations)
    {
        $locations->fill($request->only($locations->getFillable()));
        auth()->user()->locations()->save($locations);

        return $locations;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Locations  $locations
     * @return \Illuminate\Http\Response
     */
    public function show(Locations $locations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Locations  $locations
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Locations $locations)
    {
        Log::info($locations->toArray());
        // $locations->fill($request->only($locations->getFillable()))->save();
        return response(['message' => "Location has been updated successfully!."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locations  $locations
     * @return \Illuminate\Http\Response
     */
    public function destroy(Locations $locations)
    {
        // $locations->delete();

        return response(['message' => 'Location has been delete.!']);
    }

    public function getLocation($lat, $lng) 
    {
        if(!request('app', false)){
            return file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat%2C$lng&language=en&key=AIzaSyAHPUufTlBkF5NfBT3uhS9K4BbW2N-mkb4");
        }

        $res = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat%2C$lng&language=en&key=AIzaSyAHPUufTlBkF5NfBT3uhS9K4BbW2N-mkb4");

        $res = collect(json_decode($res, true));

        $x = array_filter($res['results'], function($el){
            return in_array('street_address', $el['types']) || in_array('premise', $el['types']) || in_array('administrative_area_level_2', $el['types']);
        });
        $x = array_shift($x);
        return $x['formatted_address'];
    }
}
