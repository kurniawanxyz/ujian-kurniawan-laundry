<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends BaseModel
{
    use HasFactory;

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class,"owner_id","id");
    }


    public function cashiers(): BelongsToMany
    {
        return $this->belongsToMany(Outlet::class,"outlet_cashiers","outlet_id","cashier_id");
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function transactions(): HasMany
    {
         return $this->hasMany(Transaction::class);
    }

}
