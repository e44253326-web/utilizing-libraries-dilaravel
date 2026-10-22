<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // GET /api/books
    public function index()
    {
        return Book::all();
    }

    // POST /api/books
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => 'required'
        ]);

        $book = Book::create($request->all());

        return response()->json($book, 201);
    }

    // GET /api/books/{id}
    public function show(Book $book)
    {
        return $book;
    }

    // PUT /api/books/{id}
    public function update(Request $request, Book $book)
    {
        $book->update($request->all());
        return $book;
    }

    // DELETE /api/books/{id} - INI YANG KO MAU KATA DELETE
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            'message' => 'delete',
            'status' => 'success',
            'id' => $book->id
        ]);
    }
}