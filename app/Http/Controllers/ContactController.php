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
     * @OA\Get (
     * path="/contact/send",
     * summary="Send email",
     * description="Send email",
     * tags={"Contact"},
     *  @OA\RequestBody(
     *     required=true,
     *     description="Pass user credentials",
     *     @OA\JsonContent(
     *        required={"name","email","subject","message"},
     *        @OA\Property(property="name", type="string", example="Your name"),
     *        @OA\Property(property="email", type="string", format="email", example="user1@homeweb.com"),
     *        @OA\Property(property="subject", type="string", example="Subject of email"),
     *        @OA\Property(property="message", type="string", example="Message of email"),
     *     ),
     *  ),
     *  @OA\Response(
     *     response=200,
     *     description="Send it successfully",
     *     @OA\JsonContent(
     *       @OA\Property (property="success", type="boolean", example=true),
     *       @OA\Property (property="message", type="string", example="Mail was send"),
     *     ),
     *  ),
     *  @OA\Response(
     *     response=500,
     *     description="Error while send it",
     *     @OA\JsonContent(
     *       @OA\Property (property="success", type="boolean", example=false),
     *       @OA\Property (property="message", type="string", example="Email was not send"),
     *     ),
     *  ),
     *  @OA\Response(
     *     response=422,
     *     description="Unprocessable entity",
     *  ),
     * )
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

            return response()->json(['succes' => false, 'message' => 'Email was not send'], Response::HTTP_UNPROCESSABLE_ENTITY);

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
