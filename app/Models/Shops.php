<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shops extends Model
{
    use HasFactory;
    protected $table='shops';
    protected $fillable=[
        'id',
        'name',
        'phone',
        'seller_id',
        'email',
        'address',
        'image',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];
}
