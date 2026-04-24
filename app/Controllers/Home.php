<?php
namespace App\Controllers;

use App\Models\PublicacionModel;
use App\Models\ArchivoModel;
use App\Models\UsuarioModel;

class Home extends BaseController
{
  
    //  SECCION PUBLICACIONES 


    public function guardar_publicacion() 
    {
        // 1ro recibe todos los datos al principio
        $datos_post = $this->request->getPost();//los datos del formulario
        $archivo    = $this->request->getFile('archivo');//el archivo subido en el formulario
        $dni        = session()->get('usuario')['dni_usuario'];//el dni del usuario que lleno el formulario

        // 2do validar los datos (Codeigniter maneja la validación de archivos directo del request)
        if (!$this->_validar_datos_entrada()) {
            // Si falla la validación, volvemos al formulario con los errores
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        // 3ro pasa el archivo a la funcion de subida para obtener su id
        $id_archivo = $this->_procesar_subida_archivo($archivo);

        // 4to pasa los datos sueltos para armar el paquete limpio de la base de datos
        $datos_publicacion = $this->_preparar_datos_publicacion($datos_post, $id_archivo, $dni);

        // 5to pasar los datos limpios y las variables de control a la base de datos
        $modo   = $datos_post['modo'] ?? 'nueva';
        $id_pub = $datos_post['id'] ?? 0;
        $this->_guardar_en_bd($datos_publicacion, $modo, $id_pub);

        // 6to redireccionar con exito
        return redirect()->to('publicaciones/propias')->with('mensaje', 'Publicación subida exitosamente.');
    }

<<<<<<< HEAD
  
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
=======
>>>>>>> f251d7b (funciones del controller modularizadas)

    //Funciones privadas para organizar el codigo

    
    private function _validar_datos_entrada() //valida los datos del formulario incluyendo el archivo subido
    {
        $reglas = [
            'titulo'       => 'required|min_length[3]|max_length[100]',
            'materia'      => 'required|is_natural_no_zero',
            'tipo_recurso' => 'required',
            'archivo'      => [
                'rules'  => 'uploaded[archivo]|max_size[archivo,20480]|ext_in[archivo,pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar]',
                'errors' => [
                    'uploaded' => 'Tenes que subir un archivo.',
                    'max_size' => 'El archivo no puede superar los 20 MB.',
                    'ext_in'   => 'Formato no valido. Solo se permiten PDFs, Word, PowerPoint, imágenes o ZIPs.'
                ]
            ]
        ];

<<<<<<< HEAD
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

=======
        return $this->validate($reglas);
    }

    //procesar subida del archivo
    private function _procesar_subida_archivo($file) //recibe el archivo por el parametro, lo mueve y lo registra en la base de datos
    {
>>>>>>> f251d7b (funciones del controller modularizadas)
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('./uploads/archivos', $newName);//mueve el archivo a la carpeta indicada

            $archivoModel = new \App\Models\ArchivoModel();
            return $archivoModel->insert([
                'nombre_archivo' => $file->getClientName(),
                'ruta'           => 'uploads/archivos/' . $newName,
                'formato'        => $file->getClientMimeType()
            ]);
        }

        return null;
    }

    //prepara los datos para la publicacion
    //recibe los datos del formulario, el id del archivo subido y el dni del usuario que completo el formulario
    private function _preparar_datos_publicacion($datos_post, $id_archivo, $dni)
    {
        $data = [
            'titulo'       => $datos_post['titulo'],
            'descripcion'  => $datos_post['descripcion'],
            'tipo_recurso' => $datos_post['tipo_recurso'],
            'tipo_acuerdo' => $datos_post['tipo_acuerdo'],
            'precio'       => empty($datos_post['precio']) ? 0 : $datos_post['precio'],
            'dni_usuario'  => $dni,
            'id_materia'   => $datos_post['materia'],
            'estado'       => (isset($datos_post['estado']) && $datos_post['estado'] === 'activo') ? 1 : 0
        ];

        if ($id_archivo) {
            $data['id_archivo'] = $id_archivo;
        }

        return $data;//el array de los datos limpios
    }

    // guarda o actualiza la publicacion en la base de datos segun el modo y el id de publicacion recibido
    private function _guardar_en_bd($data, $modo, $id_pub)//recibe los datos limpios, el modo de la operacion y el id de la publicacion
    {
        $pubModel = new \App\Models\PublicacionModel();

        if ($modo === 'editar' && $id_pub > 0) {
            $pubModel->update($id_pub, $data);
        } else {
            $data['fecha_publicacion'] = date('Y-m-d');
            $pubModel->insert($data);
        }
    }
    //muestra el formulario para crear una nueva publicacion
    public function nueva_publicacion() {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        return view('formulario_publicacion', [
            'usuario' => session()->get('usuario')
        ]);
    }
    

<<<<<<< HEAD
    // esta funcion muestra el formulario relleno para Editar ---
=======
    //se encarga de mostrar el formulario para editar una publicacion
>>>>>>> f251d7b (funciones del controller modularizadas)
 public function editar_publicacion($id) {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        // Usamos Query Builder para unir la publicación con su archivo
        $db = \Config\Database::connect();
        $builder = $db->table('publicacion p');
        $builder->select('p.*, a.nombre_archivo'); //Traemos el nombre del archivo
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
<<<<<<< HEAD
    // ---esta funcion passa a inactivo el archivo  ---
=======
    //cambia el estado de la publicacion a inactiva en lugar de eliminarla de la base de datos
>>>>>>> f251d7b (funciones del controller modularizadas)
    public function eliminar_publicacion($id) {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        $pubModel = new \App\Models\PublicacionModel();
        $publicacion = $pubModel->find($id);

        // verifica que la publicacion exista y sea del usuario
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
    
    public function index() {
        return view('login');
    }

      public function login()
    {
        // 1. Recibir datos del formulario (login.php)
        $dni = $this->request->getPost('dni');
        $password = $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();
        
        // 2. Buscar al usuario por su DNI en la base de datos
        $usuario = $usuarioModel->find($dni);

        // 3. Validar si existe y si la contraseña coincide
        // (Nota: En producción, usá password_hash() para guardar y password_verify() para comparar)
        if ($usuario && $password === $usuario['contrasena']) {
            
            // 4. Crear la sesión con los datos del usuario
            session()->set([
                'isLoggedIn' => true,
                'usuario'    => $usuario
            ]);

            // 5. Redirigir al panel de publicaciones
            return redirect()->to('publicaciones/propias');
        } else {
            // 6. Si falla, volver al login con error
            return redirect()->back()->with('error', 'DNI o contraseña incorrectos');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

}