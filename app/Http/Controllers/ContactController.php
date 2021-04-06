<?php

namespace App\Http\Controllers;

use App\Mail\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContactController
 * @package App\Http\Controllers
 */
class ContactController extends Controller
{
    /**
     * @param Request $request
     * @param Contact $sender
     * @return JsonResponse
     */
    public function send(Request $request, Contact $sender): JsonResponse
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);

        if ($validate->fails()) {

            return response()->json(['succes' => false, 'message' => 'Email was not send'], Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        $data = new \stdClass();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->subject = $request->subject;
        $data->message = $request->message;
        $data->send_it = now();

        Mail::to(config('mail.from.address'))->send(new Contact($data));

        return response()->json(['success' => true, 'message' => 'Mail was send'], Response::HTTP_OK);
    }
}
