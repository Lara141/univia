<?php

namespace App\Controllers;

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // La raíz de tu sistema ahora carga el dashboard de publicaciones
       // return view('mis_publicaciones');
         //return view('formulario_registro');
         return view ('login');
    }
    public function publicaciones(): string
    {
        // Esta función se encarga de mostrar tus publicaciones
        return view('mis_publicaciones');
    }

    public function nueva_publicacion(): string
    {
        // Una función dedicada exclusivamente al formulario
        return view('formulario_publicacion');
    }

     public function registro(): string
    {
        // Una función dedicada exclusivamente al formulario
        return view('formulario_registro');
    }
}