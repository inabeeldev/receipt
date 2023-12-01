<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessUnsavedMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Retrieve unsaved messages from the cache
        $users = Cache::get('unsaved_messages_users', []);

        foreach ($users as $userId => $messages) {
            foreach ($messages as $message) {
                // Persist the message to the database
                Message::create($message);
            }
        }

        // Clear the cache after processing the messages
        Cache::forget('unsaved_messages_users');
    }
}
