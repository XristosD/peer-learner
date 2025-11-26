<?php

namespace App\Actions\Book;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class CreateBookAction
{
    public function execute(array $data): Book
    {
        $book = Auth::user()->books()->create($data);

        return $book;
    }
}
