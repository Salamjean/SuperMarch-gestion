<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_method',
        'reference',
        'amount_received',
        'change_amount',
        'cash_session_id',
        'customer_id',
        'status',
        'refunded_amount',
        'synced'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
    }
}
