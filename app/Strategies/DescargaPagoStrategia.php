<?php
namespace App\Strategies;
use App\Services\PagoService;

class DescargaPagoStrategia implements DescargaStrategiaInterface
{
    protected PagoService $pagoService;

    public function __construct(PagoService $pagoService)
    {
        $this->pagoService = $pagoService;
    }
 
    public function validarAcceso(array $publicacion, string $dniUsuario): void
    {
        $idPublicacion = (int)$publicacion['id_publicacion'];
        $yaPagado = $this->pagoService->verificarPagoExistente($dniUsuario, $idPublicacion);
        
        if (!$yaPagado) {
            throw new \Exception('Acceso denegado. Requiere completar el formulario de pago.');
        }
    }
}