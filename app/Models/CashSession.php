<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opening_balance',
        'expected_closing_balance',
        'actual_closing_balance',
        'difference',
        'opened_at',
        'closed_at',
        'status',
        'synced'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function debtPayments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}
