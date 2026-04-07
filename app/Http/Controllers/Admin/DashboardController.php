<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }


    public function ckeditor()
    {
        return view('ckeditor.ckeditor-4');
    }


    public function markdown()
    {
        return view('markdown.markdown-wysiwyg-code');
    }

}
