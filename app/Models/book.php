<?php
namespace App\Http\Controllers\Api;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function index(){ return response()->json(Book::all(), 200); }
    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required',
        ]);
        $book = Book::create($validated);
        return response()->json($book, 201);
    }
    public function show($id){
        $book = Book::find($id);
        if(!$book) return response()->json(['message'=>'Book not found'],404);
        return response()->json($book,200);
    }
    public function update(Request $request, $id){
        $book = Book::find($id);
        $book->update($request->all());
        return response()->json($book,200);
    }
    public function destroy($id){
        Book::find($id)->delete();
        return response()->json(['message'=>'Book deleted'],200);
    }
}