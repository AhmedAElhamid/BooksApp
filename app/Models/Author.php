<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $author)
 */
class Author extends Model
{
    use HasFactory;

    protected $fillable = ["name"];

    public function books()
    {
        return $this->hasMany('App\Models\Book');
    }
}
