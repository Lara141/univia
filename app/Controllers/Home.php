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

  
    //  SECCIÓN PUBLICACIONES
   //Obtiene y muestra las publicaciones realizadas por el usuario logueado.
   /*
   - verifica que el usuario etse logueado
   - obtiene el DNI del usuario desde la sesión
   - utiliza Query Builder para unir las tablas publicacion, materia y archivo
   - filtra las publicaciones por el DNI del usuario
   - pasa los datos a la vista mis_publicaciones para mostrar el listado de publicaciones del usuario
   */
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

        /*
        - muestra el formulario para crear una nueva publicacion
        - veridfica que el usuario este logueado
        - pasa los datos del usuario y las publicaciones a la vista mis_publicaciones
        */
        return view('mis_publicaciones', [
            'usuario'       => session()->get('usuario'),
            'publicaciones' => $mis_publicaciones
        ]);
    }

        /*
        muestra el formulario para crear una nueva publicacion 
        - Verifica que el usuario esté logueado antes de mostrar el formulario
        - retorna la vista formulario_publicacion, pasando los datos del usuario desde la sesión para que puedan ser utilizados en el formulario (por ejemplo, para asociar la publicación al usuario que la creó)
        - envia los datos del usuario a la vista para que puedan ser utilizados en el formulario, como el DNI del usuario para asociar la publicación al usuario correcto en la base de datos cuando se guarde la publicación
        */

    public function nueva_publicacion() {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        return view('formulario_publicacion', [
            'usuario' => session()->get('usuario')
        ]);
    }

    /*
    Procesa y guarda una nueva publicación junto con su archivo adjunto
    - Obtiene el archivo enviado desde el formulario.
     - Inicializa la variable $id_archivo
    -Mueve el archivo a la carpeta uploads/archivos.
    - Guarda la información del archivo en la base de datos
    */
  public function guardar_publicacion() {
        $file = $this->request->getFile('archivo');
        $id_archivo = null;

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

        // 1. Recibimos los datos de control
        $modo   = $this->request->getPost('modo'); // 'nueva' o 'editar'
        $id_pub = $this->request->getPost('id');   // El ID real de la DB

        // 2. Preparamos los datos
        $data = [
            'titulo'       => $this->request->getPost('titulo'),
            'descripcion'  => $this->request->getPost('descripcion'),
            'tipo_recurso' => $this->request->getPost('tipo_recurso'),
            'tipo_acuerdo' => $this->request->getPost('tipo_acuerdo'),
            'precio'       => $this->request->getPost('precio') ?: 0,
            'dni_usuario'  => session()->get('usuario')['dni_usuario'],
            'id_materia'   => $this->request->getPost('materia'),
            'estado'       => $this->request->getPost('estado') === 'activo' ? 1 : 0
        ];

        if ($id_archivo) {
            $data['id_archivo'] = $id_archivo;
        }

        $pubModel = new \App\Models\PublicacionModel();

        // 3. LA DECISIÓN: ¿Update o Insert?
        if ($modo === 'editar' && $id_pub > 0) {
            // Si el modo es editar y tenemos un ID válido, actualizamos
            $pubModel->update($id_pub, $data);
        } else {
            // Si no, es una publicación nueva
            $data['fecha_publicacion'] = date('Y-m-d');
            $pubModel->insert($data);
        }

        return redirect()->to('publicaciones/propias');
    }

    // esta funcion muestra el formulario relleno para Editar ---
 public function editar_publicacion($id) {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        // Usamos Query Builder para unir la publicación con su archivo
        $db = \Config\Database::connect();
        $builder = $db->table('publicacion p');
        $builder->select('p.*, a.nombre_archivo'); // <-- Traemos el nombre del archivo
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->where('p.id_publicacion', $id);
        
        $publicacion = $builder->get()->getRowArray(); // Trae 1 sola fila

        // Seguridad: Verificar que exista y sea del usuario actual
        if (!$publicacion || $publicacion['dni_usuario'] != session()->get('usuario')['dni_usuario']) {
            return redirect()->to('publicaciones/propias');
        }

        return view('formulario_publicacion', [
            'usuario'     => session()->get('usuario'),
            'publicacion' => $publicacion,
            'modo'        => 'editar'
        ]);
    }
    // ---esta funcion passa a inactivo el archivo  ---
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
    
    //  SECCIÓN REGISTRO
   
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