<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens as HasApiTokensPassport;

class Client extends Model
{
    use  HasFactory, Notifiable, HasApiTokensPassport;



    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
