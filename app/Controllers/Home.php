<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
       return view('mis_publicaciones');
       // return view('formulario_publicacion');
        
    }
}
