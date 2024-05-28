<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model implements Authenticatable
{   
    protected $guarded = ['id'];
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;
    
    function contacts() : HasMany {
        return $this->hasMany(Contact::class , 'user_id','id');
    }

    function getAuthIdentifierName() {
        return 'username';
    }

    function getAuthIdentifier() {
        return $this->username;
    }

    function getAuthPassword() {
        return $this->password;
    }
    
    function getRememberTokenName() {
        return $this->token;
    } 

    function getRememberToken() {
        return 'token';
    }

    function setRememberToken($value) {
        return $this->token = $value;
    }
}
