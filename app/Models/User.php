<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo('App\Models\MiltaryObject');
    }

    public function userInfo(): BelongsTo
    {
        return $this->belongsTo('App\Models\UserInfo');
    }
}
