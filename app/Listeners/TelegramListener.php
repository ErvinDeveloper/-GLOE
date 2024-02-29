<?php

namespace App\Listeners;

use App\Events\SendMessageFromContactForm;
use DefStudio\Telegraph\Facades\Telegraph;

class TelegramListener
{
    public function handle(SendMessageFromContactForm $event): void
    {
        list($name, $email, $text) = array_values($event->data);
        Telegraph::message("*Name:* $name<br>*Email:* $email<br/>$text")->send();
    }
}
