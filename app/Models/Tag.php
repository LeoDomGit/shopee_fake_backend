<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $table='tags';
    protected $fillable = ['id','name', 'slug','created_at','updated_at'];
    public function brands()
    {
        return $this->morphedByMany(Brands::class, 'taggable');
    }

    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'category_tag');
    }
    public function taggables()
    {
        return $this->morphedByMany(Brands::class, 'taggable');
        return $this->morphedByMany(Categories::class, 'taggable');
        return $this->morphedByMany(Categories::class, 'taggable');

    }
}
