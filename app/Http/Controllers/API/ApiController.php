<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Vendor;
use App\Services\Api\AuthPassportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function login(Request $request, $guard, AuthPassportService $authPassportService)
    {

        $request->validate([
            'email' => 'required|email|exists:' . $guard . 's,email',
            'password' => 'required',
        ]);

        $user = $this->findUserByEmail($request->email);

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $authPassportService->requestAuthToken($request->email, $request->password, $guard);
            return $token;
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

    public function getProfile(Request $request, $guard)
    {
        return response()->json($request->user($guard));
    }




    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = $this->findUserByEmail($request->email);

        if ($user) {
            $token = $this->generateAndStoreToken($user->email);
            try {
                Mail::to($user->email)->send(new ResetPasswordMail($token));
            } catch (\Exception $ex) {
                return response()->json(['message' => $ex->getMessage()]);
            }

            return response()->json([
                'message' => trans('messages.reset-password'),
                'link' => 'http://localhost:8000/api/v1/update-password-api'
            ]);
        } else {
            return response()->json(['message' => trans('messages.not-found')], 404);
        }
    }

    public function logout(Request $request, $guard)
    {
        $token = $request->user($guard)->token();
        $token->revoke();

        return response()->json(['message' => trans('messages.logout')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = $this->findUserByEmail($request->email);

        if ($user) {
            $token = DB::table('password_reset_tokens')->where('email', $user->email)->first();;

            if ($token && $token->token == $request->token) {

                $user->password = bcrypt($request->password);
                $user->save();

                $this->deleteTokenByEmail($request->email);

                return response()->json(['message' => trans('messages.password-updated')]);
            } else {
                return response()->json(['message' => trans('messages.invalid-token')], 400);
            }
        } else {
            return response()->json(['message' => trans('messages.not-found')], 404);
        }
    }

    private function findUserByEmail($email)
    {
        $user = Vendor::where('email', $email)->first();
        if (!$user) {
            $user = Client::where('email', $email)->first();
            if (!$user) {
                $user = Admin::where('email', $email)->first();
            }
        }
        return $user;
    }

    private function generateAndStoreToken($email)
    {
        $token = Str::random(10, 1000000);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        return $token;
    }


    private function deleteTokenByEmail($email)
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}
