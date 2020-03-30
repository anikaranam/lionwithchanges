<?php 

namespace App\Observers;

use App\Models\Query\QueryHistory;
use GuzzleHttp\Client;

class QueryHistoryObserver 
{
    /**
     * Listen to the QueryHistory created event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(QueryHistory $history)
    {
			if($history->has_error) {
				return;
			}

			$payload = [
				'username' => $history->user->name,
				'data' => $history->data,
				'time' => $history->created_at->timestamp,
				'name' => $history->queryOfRecord->name,
				'structure' => $history->query_structure
			];

			$history->user->applications->each(function($application) use ($payload, $history) {
				
				$client = new Client();

				try{
					$client->request('POST', $application->callback_url, [
						'headers' => [
							'Content-Type' => 'application/json',
						],
						'json' => $payload
					]);

				} catch (\RuntimeException$e){
					\Log::error($e->getMessage(), ['History' => $history->id, 'Application' => $application->name, 'url' => $application->callback_url]);
				}
			});
    }
}
