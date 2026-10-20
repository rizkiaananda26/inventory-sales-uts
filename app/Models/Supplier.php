<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    /**
     * Relationship: Supplier has many Purchases
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
