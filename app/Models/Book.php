<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail(int $id)
 */
class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title','isbn','description'];

    public function author()
    {
        return $this->belongsTo('App\Models\Author');
    }
}
