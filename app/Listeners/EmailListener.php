<?php

namespace App\Listeners;

use App\Events\SendMessageFromContactForm;
use App\Mail\ContactUs;
use Illuminate\Support\Facades\Mail;

class EmailListener
{
    public function handle(SendMessageFromContactForm $event): void
    {
        Mail::to(env('MAIL_FROM_ADDRESS'))->send(new ContactUs($event->data));
    }
}
