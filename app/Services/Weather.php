<?php
namespace App\Services;

use GuzzleHttp\Client;

class Weather {

    public function getCurrentWeather($lat, $lng){
        $client = new Client();
        $key = env("WEATHER_API_KEY");
        $resp = $client->get("https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lng}&appid={$key}&units=imperial");
        dd($resp);
    }

}
