<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $timestamps = true;
    protected $incrementing = true;

    function user() : BelongsTo {
        return $this->belongsTo(User::class , 'user_id', 'id');
    }

    function addresses() : HasMany {
        return $this->hasMany(Address::class, 'contanct_id', 'id');
    }
}
