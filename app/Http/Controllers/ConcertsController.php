<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;

class ConcertsController extends Controller
{
    // we know the show method is going to take the concerts id
    public function show($id)
    {
        // use $id to find the concert...also need to find concerts that are not published
        // quickest way is to use a query scope
//        $concert = Concert::whereNotNull('published_at')->findOrFail($id);
        $concert = Concert::published()->findOrFail($id);
//        dd($concert);
        return view('concerts.show', ['concert' => $concert]);
    }
}
