<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'debt_balance',
        'is_credit_blocked'
    ];

    protected $casts = [
        'is_credit_blocked' => 'boolean'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function debtPayments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}
