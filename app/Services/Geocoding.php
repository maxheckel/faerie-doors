<?php

namespace App\Services;


use GuzzleHttp\Client;

class Geocoding {

    public static function getForZip(string $zip){
        $client = new Client();
        return json_decode($client->get("https://maps.googleapis.com/maps/api/geocode/json?address={$zip}&sensor=true&key=".env('MAPS_API_KEY'))->getBody()->getContents());
    }

    public static function getForLatLng($latitude, $longitude){
        $client = new Client();
        return json_decode($client->get("https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&sensor=true&key=".env('MAPS_API_KEY'))->getBody()->getContents());
    }


}
