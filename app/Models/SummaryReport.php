<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryReport
{

    private $booksAdded = array();
    private $booksFailed = array();

    public function bookAdded($book){
        array_push($this->booksAdded,
            $book['title']
        );
    }

    public function bookFailed($book){
        array_push($this->booksFailed,
            $book['title']
                ? $book['title']
                : "undefined book"
        );
    }

    public function getBooksAdded(): array
    {
        return $this->booksAdded;
    }

    public function getBooksFailed(): array
    {
        return $this->booksFailed;
    }

}
