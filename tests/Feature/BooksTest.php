<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BooksTest extends TestCase
{
    use RefreshDatabase;

    private string $booksUrl = 'api/v1/books';


    public function test_it_returns_all_books()
    {
        Author::factory()
            ->has(Book::factory()->count(3),'books')
            ->create();

        $response = $this->get($this->booksUrl);

        $response
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('msg')
                    ->has('books', 3)
            );
        $response->assertOk();
    }

    public function test_it_adds_book_to_table()
    {
        $book = $this->dummyBook();

        $response = $this->post($this->booksUrl,$book);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('msg')
                ->has('book',fn($json)=>
                $json->has('id')
                    ->where('title',$book['title'])
                    ->etc()
                )
            );
        $response->assertCreated();
    }

    public function test_it_creates_new_author_if_not_found()
    {
        $book = $this->dummyBook();
        $response = $this->post($this->booksUrl,$book);
        $response->assertCreated();

        $authors = Author::all();
        $this->assertCount(1, $authors);
    }


    public function test_it_link_book_to_author_if_the_author_name_already_exists()
    {
        $books = $this->dummyBooks();
        $response = $this->post($this->booksUrl."/multiple",['books'=>$books]);

        $response->assertCreated();

        $authors = Author::all();
        $this->assertCount(2,$authors);
    }

    public function test_it_does_not_add_book_to_table_if_isbn_is_duplicated()
    {
        $book = $this->dummyBook();

        $response = $this->post($this->booksUrl,$book);
        $response->assertStatus(201);

        $response = $this->post($this->booksUrl,$book);
        $response->assertStatus(422);
    }

    public function test_it_does_not_add_book_to_table_if_isbn_is_not_numeric()
    {
        $book = $this->dummyBook();
        $book['isbn'] = "abc";

        $response = $this->post($this->booksUrl,$book);
        $response->assertStatus(422);
    }

    public function test_it_does_not_add_book_if_not_all_fields_are_provided()
    {
        $book = [];

        $response = $this->post($this->booksUrl,$book);
        $response->assertStatus(422);
    }

    public function test_it_updates_a_book()
    {
        $book = $this->dummyBook();
        $response = $this->post($this->booksUrl,$book);
        $response->assertStatus(201);

        $bookId = $response->decodeResponseJson()['book']['id'];
        $newIsbn = '456';
        $book['isbn'] = $newIsbn;
        $response = $this->put($this->booksUrl . "/" . $bookId,$book);
        $response->assertOk();

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('msg')
                ->has('book',fn($json)=>
                $json->where('id',$bookId)
                    ->where('isbn',$newIsbn)
                    ->etc()
                )
            );

        $books = Book::all();
        $this->assertCount(1, $books);
    }

    public function test_it_deletes_a_book()
    {
        $book = $this->dummyBook();
        $response = $this->post($this->booksUrl,$book);

        $response->assertCreated();

        $bookId = $response->decodeResponseJson()['book']['id'];
        $response = $this->delete($this->booksUrl . "/" . $bookId);

        $response->assertOk();

        $books = Book::all();
        $this->assertCount(0, $books);
    }

    public function test_it_can_add_multiple_books()
    {
        $books = $this->dummyBooks();
        $response = $this->post($this->booksUrl."/multiple",['books'=>$books]);

        $response->assertCreated();

        $books = Book::all();
        $this->assertCount(3, $books);
    }


    public function test_it_only_adds_valid_books()
    {
        $books = $this->dummyBooks();
        $books[1]['isbn'] = $books[0]['isbn'];
        $response = $this->post($this->booksUrl."/multiple",['books'=>$books]);

        $response->assertCreated();

        $books = Book::all();
        $this->assertCount(2, $books);
    }


    private function dummyBook(): array
    {
        return [
            "title"=>"title 1",
            "isbn"=>"111",
            "description"=>"description 1",
            "author"=>"author #1"
        ];
    }

    private function dummyBooks(): array
    {
        return [
            [
                "title"=>"title 1",
                "isbn"=>"111",
                "description"=>"description",
                "author"=>"author #1"
            ],[
                "title"=>"title 2",
                "isbn"=>"222",
                "description"=>"description",
                "author"=>"author #2"
            ] ,[
                "title"=>"title 3",
                "isbn"=>"333",
                "description"=>"description",
                "author"=>"author #2"
            ]
        ];
    }
}
