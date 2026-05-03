<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Models\PublicacionModel;
use App\Models\UsuarioModel;

/**
 * Controlador principal de la aplicación.
 *
 * Maneja el acceso a publicaciones propias, el flujo de creación y edición
 * de publicaciones, así como el registro y el login de usuarios.
 */
class Home extends BaseController
{
    /**
     * Muestra las publicaciones del usuario autenticado.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function publicaciones()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

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
            'publicaciones' => $mis_publicaciones,
        ]);
    }

    // ==========================
    // CASO DE USO: CREAR PUBLICACIÓN
    // ==========================

    /**
     * Muestra el formulario para crear una nueva publicación.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function nueva_publicacion()
    {
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        return view('formulario_publicacion', [
            'usuario' => session()->get('usuario'),
        ]);
    }

    /**
     * Procesa el envío del formulario y crea una nueva publicación.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function guardar_publicacion()
    {
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        $datos = $this->obtenerDatosFormulario();

        if (!$this->validarDatos($datos)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $archivo = $this->crearArchivo($datos['archivo']);
        $publicacion = $this->crearPublicacion($datos, $archivo);
        $this->guardarPublicacion($publicacion);

        return redirect()->to('publicaciones/propias')
            ->with('mensaje', 'Publicación subida con éxito');
    }

    // ==========================
    // MÉTODOS DEL CASO DE USO
    // ==========================

    /**
     * Extrae los datos enviados desde el formulario de publicación.
     *
     * @return array
     */
    private function obtenerDatosFormulario()
    {
        return [
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'materia' => $this->request->getPost('materia'),
            'tipo' => $this->request->getPost('tipo_recurso'),
            'tipo_acuerdo' => $this->request->getPost('tipo_acuerdo'),
            'precio' => $this->request->getPost('precio'),
            'archivo' => $this->request->getFile('archivo'),
            'dni' => session()->get('usuario')['dni_usuario'],
        ];
    }

    /**
     * Valida los datos del formulario de publicación.
     *
     * @param array $datos
     * @return bool
     */
    private function validarDatos(array $datos)
    {
        return $this->validateData($datos, [
            'titulo' => 'required|min_length[3]',
            'materia' => 'required',
            'archivo' => 'uploaded[archivo]|max_size[archivo,20480]',
        ]);
    }

    /**
     * Guarda el archivo subido en el sistema de archivos y en la tabla archivo.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $file
     * @return int|null
     */
    private function crearArchivo($file)
    {
        if ($file && $file->isValid()) {
            $nombre = $file->getRandomName();
            $file->move('./uploads/archivos', $nombre);

            $archivoModel = new ArchivoModel();

            return $archivoModel->insert([
                'nombre_archivo' => $file->getClientName(),
                'ruta' => 'uploads/archivos/' . $nombre,
                'formato' => $file->getClientExtension(),
            ]);
        }

        return null;
    }

    /**
     * Construye los datos de publicación para insertar en la base de datos.
     *
     * @param array $datos
     * @param int|null $idArchivo
     * @return array
     */
    private function crearPublicacion(array $datos, $idArchivo)
    {
        return [
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'id_materia' => $datos['materia'],
            'tipo_recurso' => $datos['tipo'],
            'tipo_acuerdo' => $datos['tipo_acuerdo'],
            'precio' => $datos['precio'],
            'dni_usuario' => $datos['dni'],
            'id_archivo' => $idArchivo,
            'fecha_publicacion' => date('Y-m-d'),
            'estado' => 1,
        ];
    }

    /**
     * Inserta la publicación en la tabla publicacion.
     *
     * @param array $data
     * @return void
     */
    private function guardarPublicacion(array $data)
    {
        $model = new PublicacionModel();
        $model->insert($data);
    }

    /**
     * Muestra el formulario de edición para una publicación.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function editar_publicacion($id)
    {
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('publicacion p');
        $builder->select('p.*, a.nombre_archivo');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->where('p.id_publicacion', $id);

        $publicacion = $builder->get()->getRowArray();

        if (!$publicacion || $publicacion['dni_usuario'] != session()->get('usuario')['dni_usuario']) {
            return redirect()->to('publicaciones/propias');
        }

        return view('formulario_publicacion', [
            'usuario' => session()->get('usuario'),
            'publicacion' => $publicacion,
            'modo' => 'editar',
        ]);
    }

    /**
     * Marca una publicación como inactiva en lugar de eliminarla físicamente.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function eliminar_publicacion($id)
    {
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        $pubModel = new PublicacionModel();
        $publicacion = $pubModel->find($id);

        if ($publicacion && $publicacion['dni_usuario'] == session()->get('usuario')['dni_usuario']) {
            $pubModel->update($id, ['estado' => 0]);
        }

        return redirect()->to('publicaciones/propias');
    }

    // ==========================
    // UTILIDADES
    // ==========================

    /**
     * Determina si el usuario está autenticado.
     *
     * @return bool
     */
    private function usuarioLogueado()
    {
        return session()->get('isLoggedIn');
    }

    // SECCIÓN REGISTRO Y LOGIN

    /**
     * Muestra la vista de login.
     *
     * @return string
     */
    public function index()
    {
        return view('login');
    }

    /**
     * Muestra el formulario de registro.
     *
     * @return string
     */
    public function registro_vista()
    {
        return view('formulario_registro');
    }

    /**
     * Procesa el registro de un nuevo usuario.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function procesar_registro()
    {
        $usuarioModel = new UsuarioModel();
        $reglas = [
            'correo' => [
                'rules' => 'required|valid_email|is_unique[usuario.correo]',
                'errors' => [
                    'is_unique' => 'Este correo electrónico ya está en uso. Por favor, usá otro.',
                ],
            ],
            'dni' => [
                'rules' => 'required|is_unique[usuario.dni_usuario]',
                'errors' => [
                    'is_unique' => 'Este DNI ya se encuentra registrado.',
                ],
            ],
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with(
                'errores_registro', $this->validator->getErrors()
            );
        }

        $data = [
            'dni_usuario' => $this->request->getPost('dni'),
            'Nombre_usuario' => $this->request->getPost('nombre'),
            'Apellido_usuario' => $this->request->getPost('apellido'),
            'correo' => $this->request->getPost('correo'),
            'contrasena' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'id_carrera' => 1,
            'fecha_registro' => date('Y-m-d'),
            'estado' => 1,
        ];

        $usuarioModel->insert($data);

        return redirect()->to('/')->with('mensaje', '¡Registro exitoso! Ya podés iniciar sesión.');
    }

    /**
     * Valida las credenciales de login y crea la sesión de usuario.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function login()
    {
        $dni = $this->request->getPost('dni');
        $password = $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($dni);

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            session()->set([
                'isLoggedIn' => true,
                'usuario' => $usuario,
            ]);

            return redirect()->to('publicaciones/propias');
        }

        return redirect()->back()->with('error', 'DNI o contraseña incorrectos');
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/');
    }
}
