<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CacheApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves data from marvel API into the cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ts = time();
        $hash = md5(
            $ts . config('marvel.private_key'). config('marvel.public_key')
        );

        $client = new Client([
            'base_uri' => 'http://gateway.marvel.com/v1/public/',
            'query' => [
                'apikey' => config('marvel.public_key'),
                'ts' => $ts,
                'hash' => $hash
            ]
        ]);

        $endpoints = [
            'characters'
        ];

        $resultsPerPage = 20;
        $totalPageCount = 10;
        $expiresAt = Carbon::now()->addMinutes(1440); // 1 day

        foreach ($endpoints as $endpoint) {
            $data = [];
            for ($x = 0; $x <= $totalPageCount; $x++) {
                $query = $client->getConfig('query');
                $query['offset'] = $resultsPerPage * $x;

                $response = $client->get(
                    'http://gateway.marvel.com/v1/public/' . $endpoint,
                    ['query' => $query]
                );

                $response = json_decode($response->getBody(), true);

                $currentData = $response['data']['results'];

                $data = array_merge($data, $currentData);
            }

            Cache::put($endpoint, $data, $expiresAt);
        }
    }
}
