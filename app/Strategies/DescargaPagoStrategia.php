<?php
namespace App\Strategies;
use App\Services\PagoService;

/**
 * Estrategia de validación para descargas de material de pago.
 *
 * Esta clase implementa la lógica para verificar si un usuario ha pagado
 * por una publicación antes de permitir la descarga.
 */
class DescargaPagoStrategia implements DescargaStrategiaInterface
{
    /** @var PagoService Servicio para verificar la existencia de pagos. */
    protected PagoService $pagoService;

    /**
     * Constructor de la estrategia.
     *
     * @param PagoService $pagoService Inyecta el servicio de pagos para realizar las validaciones.
     */
    public function __construct(PagoService $pagoService)
    {
        $this->pagoService = $pagoService;
    } 
 
    /**
     * Valida si el usuario ha pagado por la publicación.
     *
     * @param array $publicacion Datos de la publicación que se intenta descargar.
     * @param string $dniUsuario DNI del usuario que intenta la descarga.
     * @return void
     * @throws \Exception Si no se encuentra un registro de pago para el usuario y la publicación.
     */
    public function validarAcceso(array $publicacion, string $dniUsuario): void
    {
        $idPublicacion = (int)$publicacion['id_publicacion'];
        $yaPagado = $this->pagoService->verificarPagoExistente($dniUsuario, $idPublicacion);
        
        if (!$yaPagado) {
            throw new \Exception('Acceso denegado. Requiere completar el formulario de pago.');
        }
    }
}