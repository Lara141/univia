<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
       return view('login');
       //return view('formulario_publicacion');
        
    }
}
