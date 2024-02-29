<?php

namespace App\Listeners;

use App\Events\SendMessageFromContactForm;

class TelegramListener
{
    public function handle(SendMessageFromContactForm $event): void
    {
        // здесь нужно будет организовать отправку на телегу
    }
}
