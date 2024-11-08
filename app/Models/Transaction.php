<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends BaseModel
{
    use HasFactory;

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class,"cashier_id","id");
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->invoice_code = $transaction->generateInvoiceCode();
        });
    }

    // Fungsi untuk menghasilkan kode faktur unik
    private function generateInvoiceCode()
    {
        // Format kode faktur, misalnya TRX-YYYYMMDD-RANDOM
        $date = now()->format('Ymd');
        $randomNumber = mt_rand(1000, 9999); // Menghasilkan angka acak 4 digit
        return "TRX-{$date}-{$randomNumber}";
    }
}
