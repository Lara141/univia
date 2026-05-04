<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Models\PublicacionModel;
use App\Models\UsuarioModel;
use App\Services\PublicacionService;
/**
 * este es el controlador principal de nuestra pagina
 */
class Home extends BaseController
{
    /**
     * esta funcion muestra las publicaciones que ah publicado el usuario
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

   
    // Subir material
    

    /**
     * esta funcion se encarga de mostrar el formulario para crear una publicacion 
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
     * en esta funcion procesamos el envío del formulario, y crea una nueva publicación
    
     
   * public function guardar_publicacion()
   * {
       * if (!$this->usuarioLogueado()) {
        *    return redirect()->to('/');
        *}

        *$datos = $this->obtenerDatosFormulario();

       * if (!$this->validarDatos($datos)) {
         *   return redirect()->back()
             *   ->withInput()
              *  ->with('errores', $this->validator->getErrors());
      *  }

       * $archivo = $this->crearArchivo($datos['archivo']);
       * $publicacion = $this->crearPublicacion($datos, $archivo);
       * $this->guardarPublicacion($publicacion);

       * return redirect()->to('publicaciones/propias')
       *     ->with('mensaje', 'Publicación subida con éxito');
    *}

*/
public function guardar_publicacion()
{
    if (!$this->usuarioLogueado()) {
        return redirect()->to('/');
    }

    try {
        $datos = [
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'materia' => $this->request->getPost('materia'),
            'tipo' => $this->request->getPost('tipo_recurso'),
            'tipo_acuerdo' => $this->request->getPost('tipo_acuerdo'),
            'precio' => $this->request->getPost('precio'),
            'dni' => session()->get('usuario')['dni_usuario'],
        ];

        $archivo = $this->request->getFile('archivo');

        $service = new PublicacionService();
        $service->procesarPublicacion($datos, $archivo);

        return redirect()->to('publicaciones/propias')
            ->with('mensaje', 'Publicación subida con éxito');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}


    // metodos usados en la funcion guardar_publicacion

    /**
     * en esta funcion extraemos los datos enviados desde el formulario de publicacion
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
     * en este metodo se validan los datos del formulario de publicacion
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
     * guardamos el archivo subido en la tabla de archivos y retornamos su id para asociarlo a la publicacion
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
     * este metodo construye los datos de publicación para insertar en la base de datos.
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
     * esta funcion se encarga de guardar la publicacion en la base de datos usando el modelo de publicacion
     */
    private function guardarPublicacion(array $data)
    {
        $model = new PublicacionModel();
        $model->insert($data);
    }

    /**
     * en el caso de que el usuario quiera editar una publicacion, este metodo se encarga de mostrar el formulario con los datos ya cargados 
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
     * este metodo nos permite eliminar una publicacion, pasando su estado de activo a inactivo con lo cual no se mostrara a otros usuarios
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

  

    /**
     *esta funcion verifica si el usuario inicio sesion
     */
    private function usuarioLogueado()
    {
        return session()->get('isLoggedIn');
    }

    // inicio de sesion y registro

    /**
     * esta funcion solo se encarga de mostrar el formulario de inicio de sesion
     */
    public function index()
    {
        return view('login');
    }

    /**
     *esta funcion muestra el formulario de registro
     */
    public function registro_vista()
    {
        return view('formulario_registro');
    }

    /**
     * esta funcion se encarga de procesar el registro validando los datos y guardandolos en la bd
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
     * esta funcion procesa el inicio de sesion, validando los datos ingresados
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
     * esta funcion solo se encarga de cerrar la sesion y redirigir al formulario de inicio de sesion
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/');
    }


    public function api_materias()
{
    $db = \Config\Database::connect();
    $materias = $db->table('materia')->get()->getResultArray();

    return $this->response->setJSON($materias);
}

public function api_tipos()
{
    $tipos = [
        ['id' => 1, 'nombre' => 'Apunte'],
        ['id' => 2, 'nombre' => 'Resumen'],
        ['id' => 3, 'nombre' => 'Examen']
    ];

    return $this->response->setJSON($tipos);
}

public function api_acuerdos()
{
    $acuerdos = [
        ['id' => 1, 'nombre' => 'Gratis'],
        ['id' => 2, 'nombre' => 'Pago']
    ];

    return $this->response->setJSON($acuerdos);
}
/*¿Qué hace esto?

* Devuelve datos en JSON
* No depende de una vista
* Puede ser consumido por cualquier frontend
*/

}
