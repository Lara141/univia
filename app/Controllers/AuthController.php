<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

/**
 * Controlador de Autenticación
 * Maneja login, registro y logout de usuarios
 */
class AuthController extends BaseController
{
    /**
     * Muestra el formulario de inicio de sesión
     */
    public function index()
    {
        return view('login');
    }

    /**
     * Procesa el inicio de sesión
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
     * Muestra el formulario de registro
     */
    public function registro_vista()
    {
        return view('formulario_registro');
    }

    /**
     * Procesa el registro de nuevo usuario
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
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
