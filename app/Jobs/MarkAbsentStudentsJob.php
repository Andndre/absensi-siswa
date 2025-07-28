<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class MarkAbsentStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;

    /**
     * Create a new job instance.
     */
    public function __construct($date = null)
    {
        $this->date = $date ?: Carbon::today()->format('Y-m-d');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Artisan::call('attendance:mark-absent', [
            'date' => $this->date
        ]);
    }
}
