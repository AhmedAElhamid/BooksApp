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

    private $booksUrl = 'api/v1/books';
    /**
     * A basic feature test example.
     *
     * @return void
     */
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
        $response->assertStatus(200);
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
        $response->assertStatus(201);
    }

    public function test_it_does_not_add_book_to_table_if_isbn_is_duplicated()
    {
        $book = $this->dummyBook();

        $response = $this->post($this->booksUrl,$book);
        $response->assertStatus(201);

        $response = $this->post($this->booksUrl,$book);
        $response->assertSessionHasErrors([
            'isbn' => 'The isbn has already been taken.'
        ]);
    }

    public function test_it_does_not_add_book_to_table_if_isbn_is_not_numeric()
    {
        $book = $this->dummyBook();
        $book['isbn'] = "abc";

        $response = $this->post($this->booksUrl,$book);
        $response->assertSessionHasErrors([
            'isbn' => 'The isbn must be a number.'
        ]);
    }

    public function test_it_does_not_add_book_if_not_all_fields_are_provided()
    {
        $book = [];

        $response = $this->post($this->booksUrl,$book);
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'isbn' => 'The isbn field is required.',
            'description' => 'The description field is required.',
            'author' => 'The author field is required.'
        ]);
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
        $response->assertStatus(200);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->has('msg')
                ->has('book',fn($json)=>
                $json->where('id',$bookId)
                    ->where('isbn',$newIsbn)
                    ->etc()
                )
            );

        $result = Book::all();
        $this->assertCount(1, $result);
    }

    public function test_it_deletes_a_book()
    {
        $book = $this->dummyBook();
        $response = $this->post($this->booksUrl,$book);
        $response->assertStatus(201);

        $bookId = $response->decodeResponseJson()['book']['id'];
        $this->delete($this->booksUrl . "/" . $bookId);

        $result = Book::all();
        $this->assertCount(0, $result);
    }

    public function test_it_can_add_multiple_books()
    {
        $books = $this->dummyBooks();
        $this->post($this->booksUrl."/multiple",['books'=>$books]);

        $result = Book::all();
        $this->assertCount(3, $result);
    }

    public function test_it_only_adds_valid_books()
    {
        $books = $this->dummyBooks();
        $books[1]['isbn'] = $books[0]['isbn'];
        $this->post($this->booksUrl."/multiple",['books'=>$books]);

        $result = Book::all();
        $this->assertCount(2, $result);
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