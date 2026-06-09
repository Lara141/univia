<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Services\AuthService;
use App\Services\ArchivoService;
use App\Services\PublicacionService;

/**
 * Controlador de publicaciones
 *  
 * Responsable de:
 *   - CRUD completo de publicaciones (crear, leer, actualizar, eliminar)
 *   - Gestión de publicaciones del usuario autenticado
 *   - Búsqueda y filtrado de publicaciones
 *   - Manejo de archivos adjuntos
 * 
 * Funcionalidades principales:
 *   - propias(): Muestra publicaciones del usuario logueado
 *   - crear(): Formulario para nueva publicación
 *   - guardar(): Procesa creación de publicación
 *   - editar(): Formulario de edición
 *   - actualizar(): Procesa actualización
 *   - eliminar(): Marca como inactiva
 *   - buscar(): Búsqueda con filtros (materia, tipo, palabra clave)
 * 
 */
class PublicacionController extends BaseController
{
    protected PublicacionService $publicacionService;
    protected AuthService $authService;

    /**
     * Constructor del controlador
     * 
     * Inicializa las dependencias necesarias:
     *   - ArchivoService para gestión de archivos
     *   - PublicacionService para lógica de publicaciones
     */
    public function __construct()
    {
        $archivoService = new ArchivoService(new ArchivoModel());
        $this->authService = new AuthService();
        $this->publicacionService = new PublicacionService($archivoService);
    }

    /**
     * Muestra las publicaciones del usuario autenticado
     * @param string $dni El DNI del usuario cuyas publicaciones se quieren ver.
     */
    public function propias($dni)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        // 1. Obtener el usuario autenticado desde la fuente de confianza (la sesión)
        $usuario_autenticado = $this->authService->getUsuarioAutenticado();

        if (!$usuario_autenticado) {
            return redirect()->to('/');
        }

        // 2. ¡Verificación de seguridad CRÍTICA!
        // Comparamos el DNI de la URL con el DNI de la sesión.
        if ($usuario_autenticado['dni_usuario'] != $dni) {
            // Si no coinciden, es un intento de acceso no autorizado. Redirigimos a su propia página.
            return redirect()->to('publicaciones/propias/' . $usuario_autenticado['dni_usuario'])
                             ->with('error', 'No tienes permiso para ver esta página.');
        }
 
        $mis_publicaciones = $this->publicacionService->obtenerPublicacionesUsuario($dni, false);

        return view('mis_publicaciones', [
            'usuario' => $usuario_autenticado,
            'publicaciones' => $mis_publicaciones,
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva publicación.
     * Si no se provee un DNI en la URL, redirige a la URL correcta del usuario logueado.
     * @param string|null $dni El DNI del usuario. Opcional para redirigir.
     */
    public function crear($dni = null)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        $usuario_autenticado = $this->authService->getUsuarioAutenticado();
        if (!$usuario_autenticado) {
            return redirect()->to('/');
        }

        // Si no se pasa DNI en la URL, redirigimos a la URL correcta para este usuario.
        if ($dni === null) {
            return redirect()->to('publicaciones/crear/' . $usuario_autenticado['dni_usuario']);
        }

        // Verificación de seguridad: el DNI de la URL debe coincidir con el de la sesión.
        if ($usuario_autenticado['dni_usuario'] != $dni) {
            return redirect()->to('publicaciones/crear/' . $usuario_autenticado['dni_usuario'])
                             ->with('error', 'Acción no permitida.');
        }

        return view('formulario_publicacion', [
            'usuario' => $usuario_autenticado,
        ]);
    }

    /**
     * Procesa el envío del formulario y crea una nueva publicación.
     * @param string $dni El DNI del usuario que está creando la publicación, para verificación.
     */
    public function guardar($dni)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        try {
            $usuario = $this->authService->getUsuarioAutenticado();
            if (!$usuario) {
                throw new \Exception('Sesión inválida. Por favor, inicie sesión de nuevo.');
            }

            // Verificación de seguridad CRÍTICA: el DNI de la URL debe coincidir con el de la sesión.
            if ($usuario['dni_usuario'] != $dni) {
                throw new \Exception('No tienes permiso para realizar esta acción.');
            }

            $datos = [
                'titulo' => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'materia'      => $this->request->getPost('materia'),
                'tipo_recurso' => $this->request->getPost('tipo_recurso'),
                'tipo_acuerdo' => $this->request->getPost('tipo_acuerdo'),
                'precio'       => $this->request->getPost('precio'),
                'formato_archivo' => $this->request->getPost('formato_archivo'),
            ];

            $archivo = $this->request->getFile('archivo');
            $imagenPortada = $this->request->getFile('imagen_portada');

            // Pasamos ambos archivos al servicio. El servicio decidirá cuál usar.
            $this->publicacionService->procesarPublicacion($datos, $archivo, $imagenPortada, $usuario['dni_usuario']);

            return redirect()->to('publicaciones/propias/' . $usuario['dni_usuario'])
                ->with('mensaje', 'Publicación subida con éxito');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar una publicación existente
     */
    public function editar($id)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        if (!($datos = $this->_verificarPropietario($id))) {
            return redirect()->to('publicaciones/propias/' . $this->authService->getUsuarioAutenticado()['dni_usuario'])->with('error', 'Acción no permitida.');
        }

        return view('formulario_publicacion', [
            'usuario' => $datos['usuario'],
            'publicacion' => $datos['publicacion'],
            'modo' => 'editar',
        ]);
    }

    /**
     * Procesa la actualización de una publicación
     */
    public function actualizar($id)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        try {
            if (!($datos_verificacion = $this->_verificarPropietario($id))) {
                throw new \Exception('No tienes permisos para editar esta publicación o la publicación no existe.');
            }

            $datos = [
                'titulo' => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'id_materia' => $this->request->getPost('materia'),
                'tipo_recurso' => $this->request->getPost('tipo_recurso'), // El valor es el 'slug'
                'tipo_acuerdo' => $this->request->getPost('tipo_acuerdo'),
                'precio' => $this->request->getPost('precio'),
                'estado' => $this->request->getPost('estado'),
            ];

            $archivo = $this->request->getFile('archivo');
            if ($archivo && $archivo->isValid()) {
                $formatoSlug = $this->request->getPost('formato_archivo');
                $idArchivo = $this->publicacionService->procesarArchivo($archivo, $formatoSlug);
                $datos['id_archivo'] = $idArchivo;
            }

            $this->publicacionService->actualizarPublicacion($id, $datos);

            return redirect()->to('publicaciones/propias/' . $datos_verificacion['usuario']['dni_usuario'])
                ->with('mensaje', 'Publicación actualizada con éxito');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Elimina una publicación (marca como inactiva)
     */   
    public function eliminar($id)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        $usuario = $this->authService->getUsuarioAutenticado();
        if (!$usuario) {
            return redirect()->to('/')->with('error', 'Sesión no válida.');
        }

        $redirectUrl = 'publicaciones/propias/' . $usuario['dni_usuario'];

        try {
            if (!$this->_verificarPropietario($id)) {
                return redirect()->to($redirectUrl)->with('error', 'No tienes permiso para eliminar esta publicación o no existe.');
            }
            $this->publicacionService->marcarPublicacionInactiva($id);
            return redirect()->to($redirectUrl)->with('mensaje', 'Publicación eliminada con éxito.');
        } catch (\Throwable $e) { // Usamos Throwable para capturar tanto Errores como Excepciones en PHP 7+
            log_message('error', "Error al eliminar la publicación {$id}: " . $e->getMessage());
            return redirect()->to($redirectUrl)->with('error', 'Ocurrió un error al eliminar la publicación.');
        }
    }

    /**
     * Verifica si el usuario logueado es el propietario de la publicación.
     * Centraliza la lógica de seguridad para editar, actualizar y eliminar.
     *
     * @param int $idPublicacion El ID de la publicación a verificar.
     * @return array|null Un array con 'usuario' y 'publicacion' si es válido, o null si no.
     */
    private function _verificarPropietario($idPublicacion): ?array
    {
        $usuario = $this->authService->getUsuarioAutenticado();
        if (!$usuario) {
            return null; // No hay sesión activa
        }

        $publicacion = $this->publicacionService->obtenerPublicacionPorId((int)$idPublicacion);

        if (!$publicacion || $publicacion['dni_usuario'] != $usuario['dni_usuario']) {
            return null; // La publicación no existe o no pertenece al usuario
        }

        return ['usuario' => $usuario, 'publicacion' => $publicacion];
    }
}
