<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Utils\ControllerUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookController extends Controller
{

    private function bookValidationRules(): array
    {
        return [
            'title'=>'required',
            'isbn'=>'required|unique:books',
            'description'=>'required',
            'author'=>'required',
        ];
    }

    private function mapRequestToBook(Request $request,$book=null)
    {
        $book['title'] = $request->input('title');
        $book['isbn'] = $request->input('isbn');
        $book['description'] = $request->input('description');
//        $book['author'] = $request->input('author');
        return $book;
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $books = Book::with('author')->get();

        $response =
            ControllerUtils::mapDataToResponse(["books"=>$books],
                "List of all books");
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,$this->bookValidationRules());

        $book = $this->mapRequestToBook($request,[]);

        $book = new Book($book);

        $author = $request->input('author');
        $authorInDb = Author::where('name',$author)->first();
        if(!$authorInDb){
            $authorInDb = new Author(["name"=>$author]);
            $authorInDb->save();
        }
        $authorInDb->books()->save($book);

        $book['view_book']=
            ControllerUtils::getDataLink('books/'.$book['id']);
        $response =
            ControllerUtils::mapDataToResponse(["book"=>$book],"Book Added");
        return response()->json($response,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $book = [];
        $response =
            ControllerUtils::mapDataToResponse(["book"=>$book],"Book Information");
        return response()->json($response);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $book = Book::with('author')->findOrFail($id);
        $rules = $this->bookValidationRules();
        $request->input('isbn') == $book->isbn
            ? $rules['isbn'] = 'required'
            : $rules['isbn'] = 'required|unique:books';

        $this->validate($request,$rules);

        $book = $this->mapRequestToBook($request,$book);

        $author = $request->input('author');
        $authorInDb = Author::where('name',$author)->first();
        if(!$authorInDb){
            $authorInDb = new Author(["name"=>$author]);
            $authorInDb->save();
        }
        if(!$authorInDb->books()->save($book))
            return response()->json(['error while updating book'],401);

        $book['view_book']=
            ControllerUtils::getDataLink('books/'.$book['id']);
        $response =
            ControllerUtils::mapDataToResponse(["book"=>$book],"Book Updated");
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $create =
            ControllerUtils::getDataLink("books",
                "POST",
                'title,description,isbn,author_name');
        $response = ControllerUtils::mapDataToResponse(["create"=>$create],
            "Book Deleted");
        return response()->json($response);
    }
}
