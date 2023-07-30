<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApiController extends Controller
{



    public function login(Request $request, $guard)
    {

        $request->validate([
            'email' => 'required|email|exists:' . $guard . 's,email',
            'password' => 'required',
        ]);
        $user = app("App\\Models\\$guard")->where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            $response = Http::asForm()->post('http://127.0.0.1:88/oauth/token', [
                'grant_type' => 'password',
                'client_id' => config('passport.' . $guard . '.client_id'),
                'client_secret' => config('passport.' . $guard . '.client_secret'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ]);

            return $response->json();

        } else {
            return response()->json(['error' => trans('messages.not-found')], 401);
        }
    }




    public function register(Request $request, $guard)
    {
        if (!in_array($guard, ['client', 'vendor'])) {
            return response()->json(['error' => __('can not register this type of user')]);
        }
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . $guard . 's',
            'password' => 'required|string|min:6',
        ];

        $request->validate($rules);

        $user = app("App\\Models\\$guard")->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),

        ]);

        return response()->json(['message' => trans('messages.create-user'), 'user' => $user]);
    }





    public function logout(Request $request, $guard)
    {
        $token = $request->user($guard)->token();

        $token->revoke();

        return response()->json(['message' => trans('messages.logout')]);
    }



    public function getProfile(Request $request, $guard)
    {
        return response()->json($request->user($guard));
    }




    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = Vendor::where('email', $request->email)->first();
        $user = Client::where('email', $request->email)->first();
        $user = Admin::where('email', $request->email)->first();

        if ($user) {

            $guard = get_class($user);


            try {


                $token = Str::random(10, 1000000);

                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
                $ss = DB::table('password_reset_tokens')->insert([
                    'email' => $user->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);





                try {

                    Mail::to($user->email)->send(new ResetPasswordMail($token));
                } catch (\Exception $ex) {
                    return response()->json([
                        'message' => $ex->getMessage(),
                    ]);
                }


                return response()->json([
                    'message' => trans('messages.reset-password'),
                    'link' => 'http://localhost:8000/api/v1/update-password-api'
                ]);
            } catch (\Exception $ex) {
                $arr = array("message" => $ex->getMessage());
            }
        } else {
            return response()->json(['message' => trans('messages.not-found')], 404);
        }
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'

        ]);


        $user = Vendor::where('email', $request->email)->first();
        $user = Client::where('email', $request->email)->first();
        $user = Admin::where('email', $request->email)->first();


        if ($user) {

            $guard = get_class($user);

            $token = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if ($token) {

                if ($token->token == $request->token) {

                    $user->password = bcrypt($request->password);
                    $user->save();

                    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

                    return response()->json([
                        'message' => trans('messages.password-updated'),
                    ]);
                } else {
                    return response()->json([
                        'message' => trans('messages.invalid-token'),
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => trans('messages.invalid-token'),
                ], 400);
            }
        } else {
            return response()->json(['message' => trans('messages.not-found')], 404);
        }
    }
}
