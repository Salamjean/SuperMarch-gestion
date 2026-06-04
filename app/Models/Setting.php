<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name',
        'phone',
        'address',
        'email',
        'invoice_footer',
        'invoice_format',
    ];
}
