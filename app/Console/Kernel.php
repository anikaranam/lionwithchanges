<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Query;
use App\QueryHistory;
use App\Exceptions\UserQuotaReachedException;
use Cron\CronExpression;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
			$date = Carbon::now();
			$schedule->call(function() use ($date) {
				Query::all()->each(function($query)  use ($date) {
					if($query->isDue($date->toDateTimeString())){
						try { 

							$history= new QueryHistory($query->submit());
							$history->query_structure = $query->structure;
							$history->user_id = $query->user->id;

							$query->history()->save($history);	

						} catch (UserQuotaReachedException $e){
							\Log::notice($e->getMessage(), ['user' => $query->user->email]);
						}
					}
				});	
			})->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
