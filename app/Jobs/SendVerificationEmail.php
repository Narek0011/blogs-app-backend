<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Mail\VerifyEmail;
use Exception;
use App\Models\User;

class SendVerificationEmail implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new VerifyEmail($this->user));
    }

    /**
     * Handle job failure.
     */
    public function failed(Exception $exception)
    {
        \Log::error("Job failed: " . $exception->getMessage());
    }
}
