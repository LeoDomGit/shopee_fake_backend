<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;
    protected $table='brands';
    protected $fillable=['id','shop_id','name','slug','status','content','created_at','updated_at'];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
