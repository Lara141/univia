<?php

namespace App\Services;

/**
 * Centraliza la lógica de seguridad y gestión de la sesión del usuario.
 * Es la única fuente de verdad sobre el estado de autenticación.
 *   
 * @author Sistema Univia
 * @package App\Services
 */ 
class AuthService
{
    /**
     * Verifica si el usuario actual está autenticado.
     *
     * Comprueba si existe la flag 'isLoggedIn' en la sesión.
     * Opcionalmente, puede realizar una verificación profunda para asegurar que los datos del usuario existan.
     *
     * @param bool $deepCheck Si es true, también verifica que el array de usuario en sesión no esté vacío.
     * @return bool True si está logueado, false en caso contrario.
     */
    public function estaLogueado(bool $deepCheck = false): bool
    {
        $isLoggedIn = session()->get('isLoggedIn') === true;

        if ($deepCheck) {
            return $isLoggedIn && !empty(session()->get('usuario'));
        }

        return $isLoggedIn;
    }

    /**
     * Obtiene los datos del usuario autenticado.
     *
     * Este es el método con "parámetro de salida" que mencionabas.
     * Devuelve el array completo del usuario, un campo específico, o null si no está logueado.
     *
     * @param string|null $campo El campo específico a devolver (e.g., 'dni_usuario'). Si es null, devuelve el array completo.
     * @return array|string|int|null Los datos del usuario, un valor específico, o null.
     */
    public function getUsuarioAutenticado(string $campo = null): mixed
    {
        // Usamos la verificación profunda para asegurar que tenemos datos de usuario para devolver.
        if (!$this->estaLogueado(true)) {
            return null;
        } 

        $usuario = session()->get('usuario');

        if ($campo) {
            return $usuario[$campo] ?? null;
        }

        return $usuario;
    }
}