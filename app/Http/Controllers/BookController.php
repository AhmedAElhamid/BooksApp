<?php

namespace App\Http\Controllers;

use App\Imports\BooksImport;
use App\Models\Author;
use App\Models\Book;
use App\Models\SummaryReport;
use App\Traits\SendReport;
use App\Utils\ControllerUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    use SendReport;

    private function bookValidationRules(): array
    {
        return [
            'title'=>'required',
            'isbn'=>'required|numeric|unique:books',
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
    public function index(): JsonResponse
    {
        $books = Book::with('author')->get();

        foreach ($books as $book)
            $book['view_book']=
                ControllerUtils::getDataLink('books/'.$book['id']);

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
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $book = $this->storeBook($request->all());

        $book['view_book']=
            ControllerUtils::getDataLink('books/'.$book['id']);
        $response =
            ControllerUtils::mapDataToResponse(["book"=>$book],"Book Added");
        return response()->json($response,201);
    }

    /**
     * @throws ValidationException
     */
    private function storeBook($book, $showErrorsReport=true)
    {
        $request = new Request($book);
        if($showErrorsReport){
            $this->validate($request,
                $this->bookValidationRules());
        }else{
            try {
                $this->validate($request,
                    $this->bookValidationRules());
            }catch (\Exception $ex){
                return null;
            }
        }
        $book = $this->mapRequestToBook($request);
        $book = new Book($book);

        $author = $request->input('author');
        $authorInDb = Author::where('name',$author)->first();
        if(!$authorInDb){
            $authorInDb = new Author(["name"=>$author]);
            $authorInDb->save();
        }
        return $authorInDb->books()->save($book);

    }

    /**
     * Store multiple resources in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function storeBooks(Request $request): JsonResponse
    {
        $this->validate($request,[
            'books'=>'required|array',
            'books.0'=>'required'
        ],[
            'required'=>'books must be an array of books'
        ]);
        $summaryReport = new SummaryReport();
        $books = $request->input('books');

        foreach ($books as $book)
        {
            $added = $this->storeBook($book,false);
            $added
                ? $summaryReport->bookAdded($book)
                : $summaryReport->bookFailed($book);
        }

        $this->basic_email($summaryReport);

        return response()->json(["msg"=>
            "added ". count($summaryReport->getBooksAdded()) .
            " books and " . count($summaryReport->getBooksFailed()) . " failed"],
                200);
    }


    public function storeBooksFromExcel(Request $request): JsonResponse
    {
//        $rules = array(
//            'file' => 'file|required'
//        );
//        $this->validate($request,$rules);
//        if(!$request->hasFile('file'))
//            return response()->json('not found',400);
//
//        $file = $request->file('file');
        $file = '/Users/ahmedabdelhamid/Postman/files/books.xlsx';

        (new BooksImport())
            ->queue($file);

        $summaryReport = BooksImport::getSummary();

        $this->basic_email($summaryReport);
        return response()->json(["msg"=>
            "added ". count($summaryReport->getBooksAdded()) .
            " books and " . count($summaryReport->getBooksFailed()) . " failed"]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $book = Book::findOrFail($id);
        $response =
            ControllerUtils::mapDataToResponse(["book"=>$book],"Book Information");

        return response()->json($response);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */

    public function update(Request $request, int $id): JsonResponse
    {
        $book = Book::with('author')->findOrFail($id);
        $rules = $this->bookValidationRules();
        if($request->input('isbn') == $book->isbn)
            unset($rules['isbn']);
        else
            $rules['isbn'] = 'required|unique:books';

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
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        Book::findOrFail($id)->delete();

        $create =
            ControllerUtils::getDataLink("books",
                "POST",
                'title,description,isbn,author_name');

        $response = ControllerUtils::mapDataToResponse(["create"=>$create],
            "Book Deleted");
        return response()->json($response);
    }
}
