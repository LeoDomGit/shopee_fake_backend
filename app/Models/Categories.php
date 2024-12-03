<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'status',
    ];

    // Relationship with tags (many-to-many)
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // Optionally, you can define the relationship to the Shop model if needed
    public function shop()
    {
        return $this->belongsTo(Shops::class);
    }
}
