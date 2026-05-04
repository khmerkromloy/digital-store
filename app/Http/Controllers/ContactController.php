<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(ContactRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thanks! Your message has been received. We\'ll reply within 24 hours.',
            'id' => $message->id,
        ]);
    }
}
