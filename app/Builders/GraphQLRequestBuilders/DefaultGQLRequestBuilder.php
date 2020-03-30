<?php

namespace App\Builders\GraphQLRequestBuilders;
use App\Models\GraphQLServer;
use App\Models\User;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Exceptions\ServerDownException;

class DefaultGQLRequestBuilder implements BuildsGraphQLRequests
{

	public static function build(GraphQLServer $server, User $user, String $queryString) {

		$client = new Client([
			'base_uri' => $server->url
		]);

		$headers = [
			'Content-type' => 'application/graphql'
		];

		if ( $server->requires_authorization) {

			$authorization = $user->authorizations()->where('server_id', $server->id)->firstOrFail();
			$headers['Authorization'] = json_encode([
					'accessToken' => $authorization->access_token,
					'refreshToken' => $authorization->refresh_token,
					'clientId' => config('services.'.$server->slug.'.client_id'),
					'clientSecret' => config('services.'.$server->slug.'.client_secret'),
					'meta' => $authorization->meta
			]);
		}

		try {

			//echo "Hellooooo";
			$obj = $client->request('POST', '', [
				'headers' => $headers,
				'body' => $queryString
			])->getBody()->getContents();

			echo "doneeeee";
			//return $obj;
			//echo $obj;
			try {
				//$history_data = file_get_contents('./query_history_data.txt');
				//$history_structure = file_get_contents('./query_history_structure.txt');


				$data = array("data" => "aniiii");
				$data_string = json_encode($data);
				$ch = curl_init('http://localhost:3000/addserver');                                                                      
			    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			        'Content-Type: application/json',                                                                                
			        'Content-Length: ' . strlen($data_string))                                                                 
			    );
			    curl_exec($ch);

			    echo "Hello!!";
			} catch (Exception $e) {
				echo $e->getMessage();
			}

			return $obj;

/*
			try {
			$history_data = file_get_contents('./query_history_data.txt');
			$history_structure = file_get_contents('./query_history_structure.txt');


			$data = array("data" => $history_data, "query_structure" => $history_structure, "query_id" => 1);
			$data_string = json_encode($data);
			$ch = curl_init('http://localhost:8000/addserver');                                                                      
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		        'Content-Type: application/json',                                                                                
		        'Content-Length: ' . strlen($data_string))                                                                 
		    );
		    curl_exec($ch);

		    echo "Hello!!";

		} catch (Exception $e) {
			echo $e->getMessage();
		}
*/



		} catch (ClientException $e) { // If the request results in a client error, return that
			return $e->getResponse()->getBody()->getContents();
		} catch (\Exception $e) { // If it's any other kind of error then it's not the user's fault and, thus, let them know something sad happend
			report($e);
			throw new ServerDownException($e->getMessage());
		}

		return null;
	}
}
