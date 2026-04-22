<?php
namespace App\Controllers;

use App\Models\PublicacionModel;
use App\Models\ArchivoModel;
use App\Models\UsuarioModel;

class Home extends BaseController
{
    public function index() {
        return view('login');
    }

    // ════════════════════════════════════════
    //  SECCIÓN PUBLICACIONES
    // ════════════════════════════════════════
    public function publicaciones() {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $dni = session()->get('usuario')['dni_usuario'];
        $db = \Config\Database::connect();

        $builder = $db->table('publicacion p');
        $builder->select('p.*, m.Nombre_materia, a.nombre_archivo as file_name, a.ruta');
        $builder->join('materia m', 'm.id_materia = p.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->where('p.dni_usuario', $dni);
        
        $mis_publicaciones = $builder->get()->getResultArray();

        return view('mis_publicaciones', [
            'usuario'       => session()->get('usuario'),
            'publicaciones' => $mis_publicaciones
        ]);
    }

    public function nueva_publicacion() {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        return view('formulario_publicacion', [
            'usuario' => session()->get('usuario')
        ]);
    }

  public function guardar_publicacion() {
        $file = $this->request->getFile('archivo');
        $id_archivo = null;

        // 1. Procesar archivo si existe
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('./uploads/archivos', $newName);

            $archivoModel = new \App\Models\ArchivoModel();
            $id_archivo = $archivoModel->insert([
                'nombre_archivo' => $file->getClientName(),
                'ruta'           => 'uploads/archivos/' . $newName,
                'formato'        => $file->getClientMimeType()
            ]);
        } // <-- Esta llave cierra el IF, NO la función.

        // 2. Convertir texto de estado a número (booleano) para MySQL
        $estado = $this->request->getPost('estado') === 'activo' ? 1 : 0;

        // 3. Guardar la publicación final
        $pubModel = new \App\Models\PublicacionModel();
        $pubModel->insert([
            'titulo'            => $this->request->getPost('titulo'),
            'descripcion'       => $this->request->getPost('descripcion'),
            'tipo_recurso'      => $this->request->getPost('tipo_recurso'),
            'tipo_acuerdo'      => $this->request->getPost('tipo_acuerdo'),
            'dni_usuario'       => session()->get('usuario')['dni_usuario'],
            'id_materia'        => $this->request->getPost('materia'), 
            'id_archivo'        => $id_archivo,
            'fecha_publicacion' => date('Y-m-d'),
            'estado'            => $estado
        ]);

        return redirect()->to('publicaciones/propias');
    }
     
 
    // ════════════════════════════════════════
    //  SECCIÓN REGISTRO
    // ════════════════════════════════════════
    public function registro_vista() {
        return view('formulario_registro');
    }

    public function procesar_registro() {
        $usuarioModel = new UsuarioModel();
        
        $data = [
            'dni_usuario'      => $this->request->getPost('dni'),
            'Nombre_usuario'   => $this->request->getPost('nombre'),
            'Apellido_usuario' => $this->request->getPost('apellido'),
            'correo'           => $this->request->getPost('correo'),
            'contrasena'       => $this->request->getPost('password'), 
            'id_carrera'       => 1, 
            'fecha_registro'   => date('Y-m-d'),
            'estado'           => 1
        ];

        $usuarioModel->insert($data);
        return redirect()->to('/')->with('mensaje', '¡Registro exitoso! Ya puedes iniciar sesión.');
    }
}