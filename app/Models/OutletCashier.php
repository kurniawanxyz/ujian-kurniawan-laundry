<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutletCashier extends BaseModel
{
    use HasFactory;

    public function cashier():BelongsTo
    {
        return $this->belongsTo(User::class,"cashier_id","id");
    }

    public function outlet():BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

}
