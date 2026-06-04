<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Services\ArchivoService;
use App\Services\PagoService;
use App\Services\PublicacionService;

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

    /**
     * Inicializa los servicios necesarios para validar pagos y obtener
     * información de publicaciones y archivos.
     */
    public function __construct()
    {
        $this->pagoService = new PagoService();
        $archivoService = new ArchivoService(new ArchivoModel());
        $this->publicacionService = new PublicacionService($archivoService);
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
            $yaPagado = $this->pagoService->verificarPagoExistente($dni, (int)$id);
            if (!$yaPagado) {
                return redirect()->back()->with('error', 'Acceso denegado. Requiere completar el formulario de pago.');
            }
        }

        $rutaFisica = FCPATH . $publicacion['ruta'];
        
        if (file_exists($rutaFisica)) {
            // Al retornar inline con el MIME type correcto, el navegador lo abre en otra pestaña de forma nativa
            $mime = mime_content_type($rutaFisica);

            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader(
                    'Content-Disposition',
                    'inline; filename="' . basename($rutaFisica) . '"'
                )
                ->setBody(file_get_contents($rutaFisica));
        }

        return redirect()->back()->with('error', 'El archivo no se encuentra físicamente en el servidor.');
    }
}