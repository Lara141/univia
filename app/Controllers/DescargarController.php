<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Services\ArchivoService;
use App\Services\AccesoService;


class DescargarController extends BaseController
{
    protected AccesoService $accesoService;


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