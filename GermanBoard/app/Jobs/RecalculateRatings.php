<?php

namespace App\Jobs;

use App\Models\Training;
use App\Models\TrainingRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // ← Add this
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculateRatings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $trainingId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Recalculating ratings for training ' . $this->trainingId);
        $ratings = TrainingRate::where('training_id', $this->trainingId)->get();
        $averageRating = $ratings->avg('value');
        $training = Training::find($this->trainingId);
        $training->update([
            'rate' => $averageRating?$averageRating:0 ,
        ]);
    }
}
