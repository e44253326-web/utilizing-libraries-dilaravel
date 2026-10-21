<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    // Menampilkan semua post - GET /api/books
    public function index()
    {
        return response()->json(Book::all(), 200);
    }

    // Menambahkan Data - POST /api/books
    public function store(Request $request)
    {
        $book = Book::create($request->all());
        return response()->json($book, 201);
    }

    // Menampilkan satu data - GET /api/books/{id}
    public function show($id)
    {
        return response()->json(Book::find($id), 200);
    }

    // Update Data - PUT /api/books/{id}
    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        $book->update($request->all());
        return response()->json($book, 200);
    }

    // Hapus Data - DELETE /api/books/{id}
    public function destroy($id)
    {
        Book::find($id)->delete();
        return response()->json(['message' => 'Book Deleted'], 200);
    }
}