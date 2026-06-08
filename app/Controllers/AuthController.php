<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

/**
 * Controlador de autenticacion
 * 
 * Responsable de:
 *   - Mostrar formularios de login y registro
 *   - Procesar autenticación de usuarios
 *   - Gestionar sesiones (login/logout)
 *   - Validar credenciales y datos de registro
 * 
 */
class AuthController extends BaseController
{
    /**
     * Muestra la vista del formulario de inicio de sesión.
     * Puede recibir un mensaje informativo para mostrar al usuario.
     * @param string|null $info Código del mensaje a mostrar (e.g., 'registro_exitoso').
     */
    public function index($info = null)
    {
        $data = [];
        if ($info) {
            switch ($info) {
                case 'registro_exitoso':
                    $data['mensaje'] = '¡Registro exitoso! Ya podés iniciar sesión.';
                    break;
                case 'logout_exitoso':
                    $data['mensaje'] = 'Has cerrado sesión correctamente.';
                    break;
                case 'acceso_denegado':
                    $data['error'] = 'Necesitas iniciar sesión para acceder a esa página.';
                    break;
            }
        }
        return view('login', $data);
    }

    /**
     * Procesa el inicio de sesión del usuario
     * 
     * Valida las credenciales (DNI y contraseña) contra la base de datos.
     * Si son correctas:
     *   - Crea una sesión con los datos del usuario
     *   - Redirige a publicaciones/propias
     * 
     * Si son incorrectas:
     *   - Retorna al formulario con mensaje de error
     */
    public function login($source = 'web')
    {
        // El parámetro $source se utiliza para registrar el origen del intento de login.
        log_message('info', 'Intento de login desde la fuente: ' . $source);

        $dni = $this->request->getPost('dni');
        $password = $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($dni);

        // Verificar que el usuario existe y la contraseña es correcta
        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            // Crear sesion autenticada
            session()->set([
                'isLoggedIn' => true,
                'usuario' => $usuario,
            ]); 

            return redirect()->to('publicaciones/propias/' . $usuario['dni_usuario']);
        } 

        return redirect()->back()->with('error', 'DNI o contraseña incorrectos');
    }

    /**
     * Muestra la vista del formulario de registro de nuevos usuarios
     */
    public function registro_vista($invitation_code = null)
    {
        $data = [];
        if ($invitation_code) {
            // Lógica para usar un código de invitación, por ahora solo lo logueamos y pasamos a la vista.
            log_message('info', "Acceso al registro con código de invitación: {$invitation_code}");
            $data['invitation_code'] = $invitation_code;
        }
        return view('formulario_registro', $data);
    }

    /**
     * Procesa el registro de un nuevo usuario
     * 
     * Validaciones:
     *   - Email debe ser válido y único en la BD
     *   - DNI debe ser único en la BD
     *   - Contraseña se encripta con PASSWORD_DEFAULT
     * 
     * Campos insertados:
     *   - dni_usuario, correo, contraseña (encriptada)
     *   - Nombre_usuario, Apellido_usuario
     *   - id_carrera 
     *   - fecha_registro (fecha actual)
     *   - estado (activo por defecto)
     */
   public function procesar_registro($source = 'web')
    {
        // El parámetro $source se utiliza para registrar el origen del intento de registro.
        log_message('info', 'Intento de registro desde la fuente: ' . $source);

        $usuarioModel = new \App\Models\UsuarioModel(); 
        
        $reglas = [
            'nombre' => [
                'rules'  => 'required',
                'errors' => [ 
                    'required' => 'El nombre es obligatorio.'
                ]
            ],
            'apellido' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El apellido es obligatorio.'
                ]
            ],
            'password' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'La contraseña es obligatoria.',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
                ]
            ],
            'correo' => [
                'rules' => 'required|valid_email|is_unique[usuario.correo]',
                'errors' => [
                    'required'    => 'El correo es obligatorio.',
                    'valid_email' => 'Tenés que ingresar un correo válido.',
                    'is_unique'   => 'Este correo electrónico ya está en uso. Por favor, usá otro.',
                ],
            ],
            'dni' => [
                'rules' => 'required|is_unique[usuario.dni_usuario]',
                'errors' => [
                    'required'  => 'El DNI es obligatorio.',
                    'is_unique' => 'Este DNI ya se encuentra registrado.',
                ],
            ],
        ];

        // Si la validación falla, volvemos atrás mandando los errores
        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with(
                'errores_registro', $this->validator->getErrors()
            );
        }

        // Si todo esta correcto, armamos el array para guardar
        $data = [
            'dni_usuario'      => $this->request->getPost('dni'),
            'Nombre_usuario'   => $this->request->getPost('nombre'),
            'Apellido_usuario' => $this->request->getPost('apellido'),
            'correo'           => $this->request->getPost('correo'),
            'contrasena'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'id_carrera'       => 1, 
            'fecha_registro'   => date('Y-m-d'),
            'estado'           => 1,
        ]; 

        // Insertamos en la base de datos
        $usuarioModel->insert($data);

        // Redirigimos con mensaje de exito
        return redirect()->to('/index/registro_exitoso');
    }

    /**
     * Cierra la sesión del usuario actual y redirige al inicio
     */
    public function logout($dni = null)
    {
        // Usamos el DNI para registrar quién está cerrando sesión.
        if ($dni) {
            log_message('info', "El usuario con DNI {$dni} ha cerrado sesión.");
        } else {
            log_message('info', 'Un usuario ha cerrado sesión.');
        }
        session()->destroy();
        return redirect()->to('/index/logout_exitoso');
    }

    /**
     * API: Devuelve todas las universidades en formato JSON.
     */
    public function api_universidades()
    {
        $db = \Config\Database::connect();
        $universidades = $db->table('universidad')->orderBy('nombre_universidad', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($universidades);
    }

    /**
     * API: Devuelve las carreras de una universidad específica.
     * @param int $id_universidad El ID de la universidad.
     */
    public function api_carreras($id_universidad)
    {
        if (!is_numeric($id_universidad)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID de universidad inválido']);
        }

        $db = \Config\Database::connect();
        $carreras = $db->table('carrera')
                       ->where('id_universidad', (int)$id_universidad)
                       ->orderBy('nombre_carrera', 'ASC')
                       ->get()->getResultArray();
        return $this->response->setJSON($carreras);
    }
}
