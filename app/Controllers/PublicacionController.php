<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Services\ArchivoService;
use App\Services\PublicacionService;
use App\Models\PublicacionModel;

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

    /**
     * Busca publicaciones según filtros proporcionados
     * 
     * Filtros disponibles (vía GET):
     *   - q: palabra clave para buscar en título y descripción
     *   - materia: ID de materia
     *   - tipo: tipo de recurso
     * 
     * Solo retorna publicaciones activas (estado = 1)
     * 
     * @return \CodeIgniter\HTTP\Response Vista con resultados de búsqueda
     */
    public function buscar()
    {
        $filtros = [
            'palabra_clave' => $this->request->getGet('q'),
            'materia' => $this->request->getGet('materia'),
            'tipo' => $this->request->getGet('tipo'),
        ];

        $resultados = $this->publicacionService->buscarPublicaciones($filtros);

        return view('resultados_busqueda', [
            'usuario' => session()->get('usuario'),
            'resultados' => $resultados,
        ]);
    }

    /**
     * Muestra la pantalla de exploración de materiales.
     *
     * Obtiene los filtros enviados por el usuario,
     * consulta las publicaciones que cumplen dichos criterios
     * y envía los resultados a la vista explorar_materiales.
     *
     * @return mixed
     */
    public function explorar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $filtros = [
            'palabra_clave' => $this->request->getGet('q'),
            'materia'       => $this->request->getGet('materia'),
            'tipo'          => $this->request->getGet('tipo'),
            'acuerdo'       => $this->request->getGet('acuerdo'),
            'formato'       => $this->request->getGet('formato')
        ];

        $publicaciones = $this->publicacionService->buscarPublicaciones($filtros);
        $dni = session()->get('usuario')['dni_usuario'];

        // REGLA DE TRAZABILIDAD: Verificamos de forma histórica contra la tabla 'pago'
        foreach ($publicaciones as &$pub) {
            $pub['ya_pagado'] = $this->publicacionService->verificarPagoExistente($dni, (int)$pub['id_publicacion']);
        }

        return view('explorar_materiales', [
            'usuario'       => session()->get('usuario'),
            'publicaciones' => $publicaciones,
            'filtros'       => $filtros
        ]);
    }

   /**
 * POST /publicaciones/pagar/:id
 * Procesa la transacción simulada de pago y mantiene la persistencia de los filtros.
 */
    public function procesarPago($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $dni = session()->get('usuario')['dni_usuario'];
        $publicacion = $this->publicacionService->obtenerPublicacionPorId((int)$id);

        if (!$publicacion) {
            return redirect()->back()->with('error', 'El material académico no existe.');
        }

        $monto = (float)($publicacion['precio'] ?? 0);
        
        // Insertamos el registro físico permanente en la tabla 'pago'
        $this->publicacionService->registrarNuevoPago($dni, (int)$id, $monto);

        // Redireccionamos manteniendo EXACTAMENTE los mismos filtros GET que tenía el estudiante en la URL
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        $ruta_retorno = 'publicaciones/explorar' . (!empty($query_string) ? '?' . $query_string : '');

        return redirect()->to($ruta_retorno)
                        ->with('mensaje', '¡Pago registrado con éxito! El archivo ya se encuentra desbloqueado para su descarga.');
    }

    /**
     * Endpoint de descarga segura (Diagrama 1 y 2).
     * Abre el PDF directamente en el navegador.
     */
    public function descargar($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $dni = session()->get('usuario')['dni_usuario'];
        $publicacion = $this->publicacionService->obtenerPublicacionPorId((int)$id);

        if (!$publicacion || empty($publicacion['ruta'])) {
            return redirect()->back()->with('error', 'El archivo solicitado no está disponible.');
        }

        // Regla de Negocio Crítica: Si es pago, verificar que exista el registro en la tabla pago
        if ($publicacion['tipo_acuerdo'] === 'pago') {
            $yaPagado = $this->publicacionService->verificarPagoExistente($dni, (int)$id);
            if (!$yaPagado) {
                return redirect()->back()->with('error', 'Acceso denegado. Requiere completar el formulario de pago.');
            }
        }

        $rutaFisica = FCPATH . $publicacion['ruta'];
        
        if (file_exists($rutaFisica)) {
            // Al retornar inline con el MIME type correcto, el navegador lo abre en otra pestaña de forma nativa
            return $this->response->setHeader('Content-Type', 'application/pdf')
                                ->setHeader('Content-Disposition', 'inline; filename="' . $publicacion['file_name'] . '"')
                                ->setBody(file_get_contents($rutaFisica));
        }

        return redirect()->back()->with('error', 'El archivo no se encuentra físicamente en el servidor.');
    }

}
