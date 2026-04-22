<?php
namespace App\Controllers;
use App\Models\UsuarioModel;

class Auth extends BaseController
{
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