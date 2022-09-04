<?php

namespace App\Providers;

use App\Services\AI;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AIProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(AI::class, function (){
            $client = new Client([
                'headers' => [
                    'Authorization' => 'Bearer '.env('OPEN_AI_KEY')
                ]
            ]);
            return new AI($client);
        });
    }
}
