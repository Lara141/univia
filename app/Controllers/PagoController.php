<?php

namespace App\Controllers;
use App\Services\AuthService;

use App\Models\ArchivoModel;
use App\Services\ArchivoService;
use App\Services\PublicacionService;
use App\Services\PagoService;

/**  
 * Controlador de pago simulado para publicaciones de tipo pago.
 *
 * Valida los datos ingresados por el usuario y registra la transacción
 * en la tabla de pagos para habilitar descargas posteriores.
 *
 * @package App\Controllers
 */
class PagoController extends BaseController
{
    protected PagoService $pagoService;
    protected PublicacionService $publicacionService;
    protected AuthService $authService;

    /**
     * Inicializa los servicios de pago y de publicación.
     */ 
    public function __construct()
    {
        $this->pagoService = new PagoService();
        $archivoService = new ArchivoService(new ArchivoModel());
        $this->publicacionService = new PublicacionService($archivoService);
        $this->authService = new AuthService();
    }
    
    /**
     * POST /publicaciones/pagar/:id
     *
     * Procesa el pago simulado de una publicación de pago.
     * Valida los datos del formulario, guarda el registro de pago
     * y redirige a explorar conservando los filtros GET.
     *
     * @param int|string $id Identificador de la publicación a pagar
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function procesarPago($id)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        $usuario = $this->authService->getUsuarioAutenticado();
        $dni = $usuario['dni_usuario'];
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
        
        if (!$this->pagoService->validarDatosPago($titular, $tarjeta, $vencimiento, $cvv, $metodoPago)) {
            return redirect()->back()
                ->with('error', 'Los datos de pago ingresados son inválidos.');
        }

        // Insertamos el registro físico permanente en la tabla 'pago'
        $this->pagoService->registrarNuevoPago($dni, (int)$id, $monto);

        // Redireccionamos manteniendo EXACTAMENTE los mismos filtros GET que tenía el estudiante en la URL
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        $ruta_retorno = 'publicaciones/explorar' . (!empty($query_string) ? '?' . $query_string : '');
     
        return redirect()->to($ruta_retorno)
                        ->with('mensaje', '¡Pago registrado con éxito! El archivo ya se encuentra desbloqueado para su descarga.');
    }

}
