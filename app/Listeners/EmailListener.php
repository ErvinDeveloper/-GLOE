<?php

namespace App\Listeners;

use App\Events\SendMessageFromContactForm;

class EmailListener
{
    public function handle(SendMessageFromContactForm $event): void
    {
        // здесь нужно будет организовать отправку на почту
    }
}
