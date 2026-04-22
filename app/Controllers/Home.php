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

        // 1. Procesar archivo si se subió uno nuevo
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('./uploads/archivos', $newName);

            $archivoModel = new \App\Models\ArchivoModel();
            $id_archivo = $archivoModel->insert([
                'nombre_archivo' => $file->getClientName(),
                'ruta'           => 'uploads/archivos/' . $newName,
                'formato'        => $file->getClientMimeType()
            ]);
        }

        // 2. Capturar datos del formulario
        $modo   = $this->request->getPost('modo'); // 'nueva' o 'editar'
        $id_pub = $this->request->getPost('id');   // ID de la publi si estamos editando
        $estado = $this->request->getPost('estado') === 'activo' ? 1 : 0;
        $precio = $this->request->getPost('precio') ?: 0; // Capturamos el precio!

        $data = [
            'titulo'       => $this->request->getPost('titulo'),
            'descripcion'  => $this->request->getPost('descripcion'),
            'tipo_recurso' => $this->request->getPost('tipo_recurso'),
            'tipo_acuerdo' => $this->request->getPost('tipo_acuerdo'),
            'precio'       => $precio,
            'dni_usuario'  => session()->get('usuario')['dni_usuario'],
            'id_materia'   => $this->request->getPost('materia'),
            'estado'       => $estado
        ];

        // Si subió un archivo nuevo, actualizamos el ID, sino dejamos el que estaba
        if ($id_archivo) {
            $data['id_archivo'] = $id_archivo;
        }

        $pubModel = new \App\Models\PublicacionModel();

        // 3. ¿Insertar o Actualizar?
        if ($modo === 'editar' && $id_pub) {
            $pubModel->update($id_pub, $data); // Actualiza la existente
        } else {
            $data['fecha_publicacion'] = date('Y-m-d');
            $pubModel->insert($data); // Crea una nueva
        }

        return redirect()->to('publicaciones/propias');
    }

    // --- NUEVA FUNCIÓN: Mostrar formulario relleno para Editar ---
    public function editar_publicacion($id) {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        $pubModel = new \App\Models\PublicacionModel();
        $publicacion = $pubModel->find($id);

        // Seguridad: Verificar que la publicación exista y sea del usuario
        if (!$publicacion || $publicacion['dni_usuario'] != session()->get('usuario')['dni_usuario']) {
            return redirect()->to('publicaciones/propias');
        }

        return view('formulario_publicacion', [
            'usuario'     => session()->get('usuario'),
            'publicacion' => $publicacion,
            'modo'        => 'editar' // Le avisa a la vista que active el modo edición
        ]);
    }

    // --- NUEVA FUNCIÓN: Soft Delete (Pasar a inactivo) ---
    public function eliminar_publicacion($id) {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        $pubModel = new \App\Models\PublicacionModel();
        $publicacion = $pubModel->find($id);

        // Seguridad: Verificar que sea del usuario
        if ($publicacion && $publicacion['dni_usuario'] == session()->get('usuario')['dni_usuario']) {
            // Cambiamos el estado a 0 (inactivo)
            $pubModel->update($id, ['estado' => 0]);
        }

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