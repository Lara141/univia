<?php
namespace App\Strategies;

class DescargaGratisStrategia implements DescargaStrategiaInterface
{
    public function validarAcceso(array $publicacion, string $dniUsuario): void
    {
        // El material gratis no requiere validaciones adicionales de acceso
        return; 
    }
} 