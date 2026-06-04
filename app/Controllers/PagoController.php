<?php

namespace App\Controllers;


use App\Services\PagoService;


class PagoController extends BaseController
{
    protected PagoService $pagoService;

    
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

        $titular      = $this->request->getPost('titular');
        $tarjeta      = $this->request->getPost('tarjeta');
        $vencimiento  = $this->request->getPost('vencimiento');
        $cvv          = $this->request->getPost('cvv');
        $metodoPago   = $this->request->getPost('metodo_pago');
        
        if (!$this->publicacionService->validarDatosPago($titular, $tarjeta, $vencimiento, $cvv, $metodoPago)) {
            return redirect()->back()
                ->with('error', 'Los datos de pago ingresados son inválidos.');
        }

        // Insertamos el registro físico permanente en la tabla 'pago'
        $this->publicacionService->registrarNuevoPago($dni, (int)$id, $monto);

        // Redireccionamos manteniendo EXACTAMENTE los mismos filtros GET que tenía el estudiante en la URL
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        $ruta_retorno = 'publicaciones/explorar' . (!empty($query_string) ? '?' . $query_string : '');
    
        return redirect()->to($ruta_retorno)
                        ->with('mensaje', '¡Pago registrado con éxito! El archivo ya se encuentra desbloqueado para su descarga.');
    }

}
