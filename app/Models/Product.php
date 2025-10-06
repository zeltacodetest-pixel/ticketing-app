<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
    ];

    /**
     * A product can have many tickets associated with it.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
