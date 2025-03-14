<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when(
            $title,
            fn($query, $title) => $query->title($title)
        );

        $books = match ($filter) {
            'popular_last_month' => $books->PopularLastMonth(),
            'popular_last_6months' => $books->PopularLast6Months(),
            'highest_rated_last_month' => $books->HighestRatedLastMonth(),
            'highest_rated_last_6months' => $books->HighestRatedLast6Months(),
            default => $books->latest(),
        };

        $cacheKey = 'books.' . $filter . '.' . $title;
        $books = cache()->remember($cacheKey, 3600, fn() => $books->get());


        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $cacheKey = 'books.' . $book->id;
        $book = cache()->remember($cacheKey, 3600, fn() => $book->load(['reviews' => fn($query) => $query->latest()]));
        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
