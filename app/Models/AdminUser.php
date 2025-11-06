<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $fillable = [
        'name',
        'access_token',
        'last_login',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    /**
     * Verifica se o token é válido
     */
    public static function verifyToken($token)
    {
        return self::where('access_token', $token)->first();
    }

    /**
     * Atualiza o último login
     */
    public function updateLastLogin()
    {
        $this->last_login = now();
        $this->save();
    }
}

