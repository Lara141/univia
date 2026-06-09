<?php
namespace App\Strategies;

interface DescargaStrategiaInterface
{
    /**
     * Valida si el usuario tiene acceso a descargar el archivo.
     * Si no tiene acceso, debe lanzar una excepción con el mensaje de error.
     */
    public function validarAcceso(array $publicacion, string $dniUsuario): void;
}