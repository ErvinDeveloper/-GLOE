<?php

namespace App\Http\Controllers;

use App\Events\SendMessageFromContactForm;
use App\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    public function __invoke(ContactRequest $request): void
    {
        dispatch(new SendMessageFromContactForm($request->validated()));
        //return event(new SendMessageFromContactForm($request->validated()));
    }
}
