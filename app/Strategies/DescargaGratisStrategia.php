<?php
namespace App\Strategies;

/**
 * Estrategia de validación para descargas de material gratuito.
 *
 * Implementa la interfaz de estrategia de descarga, pero como el material
 * es gratuito, no realiza ninguna validación y siempre permite el acceso.
 */
class DescargaGratisStrategia implements DescargaStrategiaInterface
{
    /**
     * Valida el acceso para una publicación gratuita.
     * Para material gratuito, el acceso siempre está permitido, por lo que este método no hace nada.
     *
     * @param array $publicacion Datos de la publicación.
     * @param string $dniUsuario DNI del usuario.
     * @return void
     */
    public function validarAcceso(array $publicacion, string $dniUsuario): void
    {
        // El material gratis no requiere validaciones adicionales de acceso
        return; 
    }
}  