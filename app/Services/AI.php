<?php

namespace App\Services;


use GuzzleHttp\Client;

class AI {

    public function __construct(public Client $client){}

    public $styles = [
        'a poem',
        'tolkien',
        'c.s. lewis',
        'a childrens story',
        'a song',
        'a folk tale',
        'brandon sanderson',
        'robert jordan'
    ];

    public function getBio(){
        $respons = $this->client->post('https://api.openai.com/v1/completions', [
            'json' => [
                'model' => 'text-davinci-002',
                'prompt' => 'What is a bio written by a faerie in first person in the style of '.$this->styles[array_rand($this->styles)].'?',
                'max_tokens' => 400
            ]
        ]);
        $resp = json_decode($respons->getBody()->getContents());
        return trim($resp->choices[0]->text);
    }
}
