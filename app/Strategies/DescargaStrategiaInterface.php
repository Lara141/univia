<?php
namespace App\Strategies;

/**
 * Interface para las Estrategias de Validación de Descarga.
 *
 * Define un contrato que todas las estrategias de descarga deben seguir.
 * Esto permite al sistema cambiar dinámicamente la lógica de validación
 * (por ejemplo, para recursos gratuitos vs. de pago) sin alterar el controlador principal.
 */
interface DescargaStrategiaInterface
{
    /**
     * Valida si el usuario tiene acceso a descargar el archivo.
     * Si no tiene acceso, debe lanzar una excepción con el mensaje de error.
     * @param array $publicacion Datos de la publicación que se intenta descargar.
     * @param string $dniUsuario DNI del usuario que intenta la descarga.
     * @return void
     * @throws \Exception Si el usuario no tiene permiso para descargar.
     */
    public function validarAcceso(array $publicacion, string $dniUsuario): void;
} 