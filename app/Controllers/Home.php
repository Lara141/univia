<?php
namespace App\Controllers;

use App\Models\PublicacionModel;
use App\Models\ArchivoModel;
use App\Models\UsuarioModel;

class Home extends BaseController
{
    // ==========================================
    // SECCIÓN PUBLICACIONES
    // ==========================================

    /*
    Obtiene y muestra las publicaciones realizadas por el usuario logueado.
    - verifica que el usuario este logueado
    - obtiene el DNI del usuario desde la sesión
    - utiliza Query Builder para unir las tablas publicacion, materia y archivo
    - pasa los datos a la vista
    */
    public function publicaciones() 
    {
        if(!session()->get('isLoggedIn')) return redirect()->to('/');

        $dni = session()->get('usuario')['dni_usuario'];
        $db = \Config\Database::connect();

        $builder = $db->table('publicacion p');
        $builder->select('p.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta');
        $builder->join('materia m', 'm.id_materia = p.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        
        $builder->where('p.dni_usuario', $dni);

        $mis_publicaciones = $builder->get()->getResultArray();
        
        return view('mis_publicaciones', [
            'usuario' => session()->get('usuario'),
            'publicaciones' => $mis_publicaciones
        ]);
    }

    /*
    Muestra el formulario para crear una nueva publicación
    */
    public function nueva_publicacion() 
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        return view('formulario_publicacion', [
            'usuario' => session()->get('usuario')
        ]);
    }

    /*
    Procesa y guarda una nueva publicación junto con su archivo adjunto
    */
    public function guardar_publicacion() 
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $datos_post = $this->request->getPost();
        $archivo    = $this->request->getFile('archivo');
        $dni        = session()->get('usuario')['dni_usuario'];
        $modo       = $datos_post['modo'] ?? 'nueva'; // Detectamos el modo aquí

        // Validar (pasamos el modo para saber si el archivo es obligatorio)
        if (!$this->_validar_datos_entrada($modo)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $id_archivo = $this->_procesar_subida_archivo($archivo);
        $datos_publicacion = $this->_preparar_datos_publicacion($datos_post, $id_archivo, $dni);

        $id_pub = $datos_post['id'] ?? 0;
        $this->_guardar_en_bd($datos_publicacion, $modo, $id_pub);

        // Personalizar el mensaje según el modo
        $mensaje = ($modo === 'editar') ? '¡Publicación editada con éxito!' : '¡Publicación subida con éxito!';

        return redirect()->to('publicaciones/propias')->with('mensaje', $mensaje);
    }

    /*
    Muestra el formulario relleno para Editar
    */
    public function editar_publicacion($id) 
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        // Usamos Query Builder para unir la publicación con su archivo
        $db = \Config\Database::connect();
        $builder = $db->table('publicacion p');
        $builder->select('p.*, a.nombre_archivo'); // Traemos el nombre del archivo
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

    /*
    Cambia el estado de la publicación a inactiva en lugar de eliminarla
    */
    public function eliminar_publicacion($id) 
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        $pubModel = new PublicacionModel();
        $publicacion = $pubModel->find($id);

        // Verifica que la publicación exista y sea del usuario
        if ($publicacion && $publicacion['dni_usuario'] == session()->get('usuario')['dni_usuario']) {
            // Cambiamos el estado a 0 (inactivo)
            $pubModel->update($id, ['estado' => 0]);
        }

        return redirect()->to('publicaciones/propias');
    }

    // ==========================================
    // MÉTODOS PRIVADOS AUXILIARES (MODULARIZACIÓN)
    // ==========================================

    private function _validar_datos_entrada($modo) 
    {
        $reglas = [
            'titulo'       => 'required|min_length[3]|max_length[100]',
            'materia'      => 'required|is_natural_no_zero',
            'tipo_recurso' => 'required',
        ];

        // Si es nueva, el archivo es obligatorio. Si es editar, es opcional.
        $reglaArchivo = ($modo === 'nueva') ? 'uploaded[archivo]|' : '';
        $reglas['archivo'] = [
            'rules'  => $reglaArchivo . 'max_size[archivo,20480]|ext_in[archivo,pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar]',
            'errors' => [
                'uploaded' => 'Tenés que subir un archivo.',
                'max_size' => 'El archivo no puede superar los 20 MB.',
                'ext_in'   => 'Formato no válido.'
            ]
        ];

        return $this->validate($reglas);
    }

    private function _procesar_subida_archivo($file) 
    {
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('./uploads/archivos', $newName);

            $archivoModel = new ArchivoModel();
            return $archivoModel->insert([
                'nombre_archivo' => $file->getClientName(),
                'ruta'           => 'uploads/archivos/' . $newName,
                'formato'        => $file->getClientMimeType()
            ]);
        }
        return null;
    }

    private function _preparar_datos_publicacion($datos_post, $id_archivo, $dni)
    {
        $data = [
            'titulo'       => $datos_post['titulo'],
            'descripcion'  => $datos_post['descripcion'] ?? '',
            'tipo_recurso' => $datos_post['tipo_recurso'],
            'tipo_acuerdo' => $datos_post['tipo_acuerdo'] ?? '',
            'precio'       => empty($datos_post['precio']) ? 0 : $datos_post['precio'],
            'dni_usuario'  => $dni,
            'id_materia'   => $datos_post['materia'],
            'estado'       => (isset($datos_post['estado']) && $datos_post['estado'] === 'activo') ? 1 : 0
        ];

        if ($id_archivo) {
            $data['id_archivo'] = $id_archivo;
        }

        return $data; // array de los datos limpios
    }

    private function _guardar_en_bd($data, $modo, $id_pub)
    {
        $pubModel = new PublicacionModel();

        if ($modo === 'editar' && $id_pub > 0) {
            $pubModel->update($id_pub, $data);
        } else {
            $data['fecha_publicacion'] = date('Y-m-d');
            $pubModel->insert($data);
        }
    }

    // ==========================================
    // SECCIÓN REGISTRO Y LOGIN
    // ==========================================

    public function index() 
    {
        return view('login');
    }

    public function registro_vista() 
    {
        return view('formulario_registro');
    }

    public function procesar_registro() 
    {
        $usuarioModel = new UsuarioModel();
        
        $reglas = [
            'correo' => [
                'rules'  => 'required|valid_email|is_unique[usuario.correo]',
                'errors' => [
                    'is_unique' => 'Este correo electrónico ya está en uso. Por favor, usá otro.'
                ]
            ],
            'dni' => [
                'rules'  => 'required|is_unique[usuario.dni_usuario]',
                'errors' => [
                    'is_unique' => 'Este DNI ya se encuentra registrado.'
                ]
            ]
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores_registro', $this->validator->getErrors());
        }

        $data = [
            'dni_usuario'      => $this->request->getPost('dni'),
            'Nombre_usuario'   => $this->request->getPost('nombre'),
            'Apellido_usuario' => $this->request->getPost('apellido'),
            'correo'           => $this->request->getPost('correo'),
            'contrasena'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), 
            'id_carrera'       => 1, 
            'fecha_registro'   => date('Y-m-d'),
            'estado'           => 1
        ];

        $usuarioModel->insert($data);
        return redirect()->to('/')->with('mensaje', '¡Registro exitoso! Ya podés iniciar sesión.');
    }

    public function login()
    {
        $dni = $this->request->getPost('dni');
        $password = $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($dni);
        
        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            session()->set([
                'isLoggedIn' => true,
                'usuario'    => $usuario
            ]);

            return redirect()->to('publicaciones/propias');
        } else {
            return redirect()->back()->with('error', 'DNI o contraseña incorrectos');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}