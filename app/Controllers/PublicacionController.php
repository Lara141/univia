<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Services\ArchivoService;
use App\Services\PublicacionService;

/**
 * ═══════════════════════════════════════════════════════════════
 * CONTROLADOR DE PUBLICACIONES
 * ═══════════════════════════════════════════════════════════════
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
 * @author Sistema Univia
 * @package App\Controllers
 */
class PublicacionController extends BaseController
{
    protected PublicacionService $publicacionService;

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
        $this->publicacionService = new PublicacionService($archivoService);
    }

    /**
     * Muestra las publicaciones del usuario autenticado
     */
    public function propias()
    {
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        $dni = session()->get('usuario')['dni_usuario'];
        $mis_publicaciones = $this->publicacionService->obtenerPublicacionesUsuario($dni, false);

        return view('mis_publicaciones', [
            'usuario' => session()->get('usuario'),
            'publicaciones' => $mis_publicaciones,
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva publicación
     */
    public function mostrarFormulario()
    {
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        return view('formulario_publicacion', [
            'usuario' => session()->get('usuario'),
        ]);
    }

    /**
     * Procesa el envío del formulario y crea una nueva publicación
     */
    public function guardar()
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

            $this->publicacionService->procesarPublicacion($datos, $archivo);

            return redirect()->to('publicaciones/propias')
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
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        $publicacion = $this->publicacionService->obtenerPublicacionPorId($id);

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
     * Procesa la actualización de una publicación
     */
    public function actualizar($id)
    {
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        try {
            $publicacion = $this->publicacionService->obtenerPublicacionPorId($id);

            if (!$publicacion || $publicacion['dni_usuario'] != session()->get('usuario')['dni_usuario']) {
                throw new \Exception('No tienes permisos para editar esta publicación');
            }

            $datos = [
                'titulo' => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'id_materia' => $this->request->getPost('materia'),
                'tipo_recurso' => $this->request->getPost('tipo_recurso'),
                'tipo_acuerdo' => $this->request->getPost('tipo_acuerdo'),
                'precio' => $this->request->getPost('precio'),
                'estado' => $this->request->getPost('estado'),
            ];

            $archivo = $this->request->getFile('archivo');
            if ($archivo && $archivo->isValid()) {
                $idArchivo = $this->publicacionService->procesarArchivo($archivo);
                $datos['id_archivo'] = $idArchivo;
            }

            $this->publicacionService->actualizarPublicacion($id, $datos);

            return redirect()->to('publicaciones/propias')
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
        if (!$this->usuarioLogueado()) {
            return redirect()->to('/');
        }

        $publicacion = $this->publicacionService->obtenerPublicacionPorId($id);

        if ($publicacion && $publicacion['dni_usuario'] == session()->get('usuario')['dni_usuario']) {
            $this->publicacionService->marcarPublicacionInactiva($id);
        }

        return redirect()->to('publicaciones/propias');
    }

    /**
     * Verifica si el usuario actual está autenticado
     * 
     * Comprueba si existe la flag 'isLoggedIn' en la sesión
     * 
     * @return bool True si está logueado, false en caso contrario
     */
    private function usuarioLogueado()
    {
        return session()->get('isLoggedIn');
    }

}
