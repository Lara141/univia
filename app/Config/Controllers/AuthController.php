<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

/**
 * ═══════════════════════════════════════════════════════════════
 * CONTROLADOR DE AUTENTICACIÓN
 * ═══════════════════════════════════════════════════════════════
 * 
 * Responsable de:
 *   - Mostrar formularios de login y registro
 *   - Procesar autenticación de usuarios
 *   - Gestionar sesiones (login/logout)
 *   - Validar credenciales y datos de registro
 * 
 * @author Sistema Univia
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Muestra la vista del formulario de inicio de sesión
     * 
     * @return \CodeIgniter\HTTP\Response Vista de login
     */
    public function index()
    {
        return view('login');
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
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a panel o login
     */
    public function login()
    {
        $dni = $this->request->getPost('dni');
        $password = $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($dni);

        // Verificar que el usuario existe y la contraseña es correcta
        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            // Crear sesión autenticada
            session()->set([
                'isLoggedIn' => true,
                'usuario' => $usuario,
            ]);

            return redirect()->to('publicaciones/propias');
        }

        return redirect()->back()->with('error', 'DNI o contraseña incorrectos');
    }

    /**
     * Muestra la vista del formulario de registro de nuevos usuarios
     * 
     * @return \CodeIgniter\HTTP\Response Vista de formulario de registro
     */
    public function registro_vista()
    {
        return view('formulario_registro');
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
     *   - dni_usuario, correo, contrasena (encriptada)
     *   - Nombre_usuario, Apellido_usuario
     *   - id_carrera (por defecto 1)
     *   - fecha_registro (fecha actual)
     *   - estado (activo por defecto)
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección con mensaje de éxito o errores
     */
   public function procesar_registro()
    {
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

        // Si todo está correcto, armamos el array para guardar
        $data = [
            'dni_usuario'      => $this->request->getPost('dni'),
            'Nombre_usuario'   => $this->request->getPost('nombre'),
            'Apellido_usuario' => $this->request->getPost('apellido'),
            'correo'           => $this->request->getPost('correo'),
            'contrasena'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'id_carrera'       => 1, // Por ahora fijo en 1
            'fecha_registro'   => date('Y-m-d'),
            'estado'           => 1,
        ];

        // Insertamos en la base de datos
        $usuarioModel->insert($data);

        // Redirigimos con mensaje de éxito
        return redirect()->to('/')->with('mensaje', '¡Registro exitoso! Ya podés iniciar sesión.');
    }

    /**
     * Cierra la sesión del usuario actual
     * 
     * Destruye todos los datos de sesión y redirige al login
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a página de login
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
