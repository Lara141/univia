<?php

namespace App\Services;

use Config\Database;

/**
 * Servicio de pago simulado.
 *
 * Administra la persistencia de pagos, la verificación de pagos realizados
 * y la validación básica de los datos de la transacción.
 *
 * @package App\Services
 */
class PagoService
{
    /**
     * Verifica si un estudiante ya pagó por una publicación específica.
     *
     * @param string $dni DNI del usuario
     * @param int $idPublicacion ID de la publicación
     * @return bool True si existe el registro de pago, false en caso contrario
     */
    public function verificarPagoExistente(string $dni, int $idPublicacion): bool
    {
        $db = \Config\Database::connect();
        $resultado = $db->table('pago')
                        ->where('dni_usuario', $dni)
                        ->where('id_publicacion', $idPublicacion)
                        ->get()
                        ->getRow();
                        
        return $resultado !== null;
    }

    /**
     * Inserta de forma física la transacción del pago simulado.
     */
    /**
     * Inserta de forma física la transacción del pago simulado.
     *  
     * @param string $dni DNI del usuario
     * @param int $idPublicacion ID de la publicación pagada
     * @param float $monto Monto de la transacción
     * @return bool True si la inserción se realizó con éxito
     */
    public function registrarNuevoPago(string $dni, int $idPublicacion, float $monto): bool
    {
        $db = \Config\Database::connect();
        return $db->table('pago')->insert([
            'dni_usuario'    => $dni,
            'id_publicacion' => $idPublicacion,
            'fecha_pago'     => date('Y-m-d'),
            'monto'          => $monto
        ]);
    }

    /**
     * Valida los datos de pago enviados por el estudiante.
     *
     * @param string $titular Nombre del titular de la tarjeta
     * @param string $tarjeta Número de tarjeta (sin espacios)
     * @param string $vencimiento Fecha de vencimiento MM/AA
     * @param string $cvv Código CVV de 3 dígitos
     * @param string $metodoPago Método de pago seleccionado
     * @return bool True si todos los datos tienen formato válido
     */
    public function validarDatosPago(string $titular, string $tarjeta, string $vencimiento, string $cvv, string $metodoPago): bool
    {
        // Titular obligatorio
        if (empty(trim($titular))) {
            return false;
        }

        // Método de pago obligatorio
        if (empty(trim($metodoPago))) {
            return false;
        }

        // Quitamos espacios de la tarjeta
        $tarjeta = str_replace(' ', '', $tarjeta);

        // Tarjeta: exactamente 16 dígitos
        if (!preg_match('/^[0-9]{16}$/', $tarjeta)) {
            return false;
        }

        // Vencimiento MM/AA
        if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $vencimiento)) {
            return false;
        }

        // CVV: 3 dígitos
        if (!preg_match('/^[0-9]{3}$/', $cvv)) {
            return false;
        } 

        return true;
    }
}
