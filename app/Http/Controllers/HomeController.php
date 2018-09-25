<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
	private $client;

	public function __construct()
	{
		$ts = time();
		$private_key = config('marvel.private_key');
       $public_key = config('marvel.public_key');
       $hash = md5($ts . $private_key . $public_key);
       $this->client = new Client([
          'base_uri' => 'https://gateway.marvel.com:443/v1/public/',
          'query' => [
             'apikey' => $public_key,
             'ts' => $ts,
             'hash' => $hash
         ]
     ]);
   }

    public function index()
    {
        $characters = Cache::get('characters');
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        if (is_null($currentPage)) {
            $currentPage = 1;
        }
        $charactersCollection = new Collection($characters);
        $itemsPerPage = 6;
        $currentPageResults = $charactersCollection
            ->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage)
            ->all();
        $paginatedResults = new LengthAwarePaginator(
            $currentPageResults,
            count($charactersCollection),
            $itemsPerPage
        );
        
        return view(
            'home',
            ['characters' => $paginatedResults, 'query' => '']
        );
    }


    public function search(Request $request)
    {
        $searchTerm = '';
        if ($request->has('query')) {
            $searchTerm = $request->input('query');

            $query = $this->client->getConfig('query');
            $query['nameStartsWith'] = $searchTerm;

            $response = $this->client->get('characters', ['query' => $query]);
            $response = json_decode($response->getBody(), true);

            $characters = $response['data']['results'];
        } else {
            $characters = Cache::get('characters');
            shuffle($characters);
            $characters = array_slice($characters, 0, 20);
        }

        return view(
            'home',
            ['characters' => $characters, 'query' => $searchTerm]
        );
    }
}
