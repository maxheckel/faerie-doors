<?php

namespace App\Services;


use GuzzleHttp\Client;

class AI {

    public function __construct(public Client $client){}

    public function getSuggestions(string $prompt){
        
    }
}
