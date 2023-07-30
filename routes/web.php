<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




// Route::post('/token', [
//     'uses' => 'AccessTokenController@issueToken',
//     'as' => 'token',
//     'middleware' => 'throttle',
// ]);

// Route::get('/authorize', [
//     'uses' => 'AuthorizationController@authorize',
//     'as' => 'authorizations.authorize',
//     'middleware' => 'web',
// ]);


// $guard = config('auth.guard', 'client');




// Route::middleware(['web', $guard ? 'auth:'.$guard : 'auth'])->group(function () {
//     Route::post('/token/refresh', [
//         'uses' => 'TransientTokenController@refresh',
//         'as' => 'token.refresh',
//     ]);

//     Route::post('/authorize', [
//         'uses' => 'ApproveAuthorizationController@approve',
//         'as' => 'authorizations.approve',
//     ]);

//     Route::delete('/authorize', [
//         'uses' => 'DenyAuthorizationController@deny',
//         'as' => 'authorizations.deny',
//     ]);

//     Route::get('/tokens', [
//         'uses' => 'AuthorizedAccessTokenController@forUser',
//         'as' => 'tokens.index',
//     ]);

//     Route::delete('/tokens/{token_id}', [
//         'uses' => 'AuthorizedAccessTokenController@destroy',
//         'as' => 'tokens.destroy',
//     ]);

//     Route::get('/clients', [
//         'uses' => 'ClientController@forUser',
//         'as' => 'clients.index',
//     ]);

//     Route::post('/clients', [
//         'uses' => 'ClientController@store',
//         'as' => 'clients.store',
//     ]);

//     Route::put('/clients/{client_id}', [
//         'uses' => 'ClientController@update',
//         'as' => 'clients.update',
//     ]);

//     Route::delete('/clients/{client_id}', [
//         'uses' => 'ClientController@destroy',
//         'as' => 'clients.destroy',
//     ]);

//     Route::get('/scopes', [
//         'uses' => 'ScopeController@all',
//         'as' => 'scopes.index',
//     ]);

//     Route::get('/personal-access-tokens', [
//         'uses' => 'PersonalAccessTokenController@forUser',
//         'as' => 'personal.tokens.index',
//     ]);

//     Route::post('/personal-access-tokens', [
//         'uses' => 'PersonalAccessTokenController@store',
//         'as' => 'personal.tokens.store',
//     ]);

//     Route::delete('/personal-access-tokens/{token_id}', [
//         'uses' => 'PersonalAccessTokenController@destroy',
//         'as' => 'personal.tokens.destroy',
//     ]);
// });








require __DIR__.'/auth.php';
