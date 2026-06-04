<?php

namespace App\Services;

use Config\Database;


class PagoService
{
   
    /**
     * Verifica si un estudiante ya pagó por una publicación específica.
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
