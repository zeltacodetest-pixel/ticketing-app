<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'title',
        'type',
        'description',
        'status',
        'assigned_to',
    ];

    // 🔹 A ticket belongs to a customer (user)
   public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}


    // 🔹 A ticket may be assigned to a developer (user)
    public function developer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // 🔹 A ticket has many media files
    public function media()
    {
        return $this->hasMany(TicketMedia::class);
    }

    // 🔹 A ticket references a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function comments()
{
    return $this->hasMany(\App\Models\TicketComment::class);
}

}
