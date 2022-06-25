<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;

class DemoController extends Controller
{
    public function Index()
    {
        return view('about');
    } // end method

    public function ContactMethod()
    {
        return view('contact');
    }
}
