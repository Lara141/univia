<?php

namespace App\Controllers;
use App\Services\AuthService;

use App\Models\ArchivoModel;  
use App\Services\ArchivoService;
use App\Services\PagoService;
use App\Services\PublicacionService;
use App\Strategies\DescargaGratisStrategia;
use App\Strategies\DescargaPagoStrategia;


/**  
 * Controlador de descarga de archivos seguros.
 * 
 * Valida acceso a recursos gratuitos y de pago, y entrega el archivo
 * inline para que el navegador lo abra directamente.
 *
 * @package App\Controllers
 */
class DescargarController extends BaseController
{ 
    protected PagoService $pagoService;
    protected PublicacionService $publicacionService;
    protected AuthService $authService;

    /**
     * Inicializa los servicios necesarios para validar pagos y obtener
     * información de publicaciones y archivos.
     */
    public function __construct()
    {
        $this->pagoService = new PagoService();
        $archivoService = new ArchivoService(new ArchivoModel());
        $this->publicacionService = new PublicacionService($archivoService);
        $this->authService = new AuthService();
    }

    /**
     * Endpoint de descarga segura (Diagrama 1 y 2).
     * Abre el PDF directamente en el navegador.
     */
    public function descargar($id)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        $usuario = $this->authService->getUsuarioAutenticado();
        $dni = $usuario['dni_usuario'];
        $publicacion = $this->publicacionService->obtenerPublicacionPorId((int)$id);

        if (!$publicacion || empty($publicacion['ruta'])) {
            return redirect()->back()->with('error', 'El archivo solicitado no está disponible.');
        }

        // 1. Diccionario de estrategias
        $estrategias = [
            'gratis' => new DescargaGratisStrategia(),
            'pago'   => new DescargaPagoStrategia($this->pagoService)
        ];

        // 2. Seleccionar la estrategia en tiempo de ejecución
        $tipoAcuerdo = $publicacion['tipo_acuerdo'];
        if (!isset($estrategias[$tipoAcuerdo])) {
            return redirect()->back()->with('error', 'Tipo de acuerdo desconocido.');
        }

        // 3. Ejecutar la estrategia
        try {
            $estrategias[$tipoAcuerdo]->validarAcceso($publicacion, $dni);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Limpiamos la ruta por si viene con './' desde la base de datos
        $rutaRelativa = $publicacion['ruta'];
        if (str_starts_with($rutaRelativa, './')) {
            $rutaRelativa = substr($rutaRelativa, 2);
        }

        $rutaFisica = WRITEPATH . $rutaRelativa;
        if (file_exists($rutaFisica)) {
           // Forzamos la descarga del archivo (attachment) con su nombre original
            $mime = mime_content_type($rutaFisica);
            $nombreOriginal = $publicacion['file_name'] ?? basename($rutaFisica);

            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader(
                    'Content-Disposition',
                        'attachment; filename="' . $nombreOriginal . '"'
                )
                ->setBody(file_get_contents($rutaFisica));
        }
 
        return redirect()->back()->with('error', 'El archivo no se encuentra físicamente en el servidor.');
    }

    /**
     * Endpoint para la vista previa de archivos.
     * Sirve el archivo con 'Content-Disposition: inline' para que el navegador
     * intente mostrarlo en lugar de descargarlo. No requiere pago.
     */
    public function preview($id)
    {
        $publicacion = $this->publicacionService->obtenerPublicacionPorId((int)$id);

        if (!$publicacion || empty($publicacion['ruta'])) {
            // Usamos una respuesta de error HTTP en lugar de una redirección
            // para que el iframe o la imagen muestren un error claro.
            return $this->response->setStatusCode(404, 'Archivo no encontrado');
        }

        $rutaRelativa = $publicacion['ruta'];
        if (str_starts_with($rutaRelativa, './')) {
            $rutaRelativa = substr($rutaRelativa, 2);
        }

        $rutaFisica = WRITEPATH . $rutaRelativa;

        if (file_exists($rutaFisica)) {
            $mime = mime_content_type($rutaFisica);

            // Para otros tipos de archivo, los servimos directamente
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline')
                ->setBody(file_get_contents($rutaFisica));
        }

        return $this->response->setStatusCode(404, 'El archivo no existe en el servidor.');
    }
}