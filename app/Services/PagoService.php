<?php

namespace App\Services;

use Config\Database;

/**
 * Responsable de:
 *   - Verificar si un usuario ya ha pagado por una publicación.
 *   - Registrar un nuevo pago en la base de datos.
 *   - Validar los datos de un formulario de pago (tarjeta de crédito).
 * 
 * @author Sistema Univia
 * @package App\Services
 */
class PagoService
{ 
    protected $db;

    /**
     * Constructor del servicio.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct()
    {
        // Centralizamos la conexión acá
        $this->db = \Config\Database::connect();
    }

    /**
     * Verifica si ya existe un pago registrado para un usuario y una publicación específicos.
     *
     * @param string $dni DNI del usuario.
     * @param int $idPublicacion ID de la publicación.
     * @return bool True si el pago existe, false en caso contrario.
     */
    public function verificarPagoExistente(string $dni, int $idPublicacion): bool
    {
        // Ahora usamos $this->db
        $resultado = $this->db->table('pago')
                        ->where('dni_usuario', $dni)
                        ->where('id_publicacion', $idPublicacion)
                        ->get()
                        ->getRow();
                        
        return $resultado !== null;
    }

    /**
     * Registra un nuevo pago en la base de datos.
     *
     * @param string $dni DNI del usuario que realiza el pago.
     * @param int $idPublicacion ID de la publicación que se está comprando.
     * @param float $monto El monto del pago.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function registrarNuevoPago(string $dni, int $idPublicacion, float $monto): bool
    {
        // Ahora usamos $this->db
        return $this->db->table('pago')->insert([
            'dni_usuario'    => $dni,
            'id_publicacion' => $idPublicacion,
            'fecha_pago'     => date('Y-m-d'),
            'monto'          => $monto
        ]);
    }

    /**
     * Valida los datos de un formulario de pago (tarjeta de crédito).
     * Realiza validaciones de formato para el titular, número de tarjeta, fecha de vencimiento y CVV.
     *
     * @param string $titular Nombre del titular de la tarjeta.
     * @param string $tarjeta Número de la tarjeta de crédito.
     * @param string $vencimiento Fecha de vencimiento en formato MM/YY.
     * @param string $cvv Código de seguridad de 3 dígitos.
     * @param string $metodoPago Método de pago seleccionado.
     * @return bool True si todos los datos son válidos, false en caso contrario.
     */
    public function validarDatosPago(string $titular, string $tarjeta, string $vencimiento, string $cvv, string $metodoPago): bool
    {
        if (empty(trim($titular))) return false;
        if (empty(trim($metodoPago))) return false;

        $tarjeta = str_replace(' ', '', $tarjeta);
        if (!preg_match('/^[0-9]{16}$/', $tarjeta)) return false;
        if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $vencimiento)) return false;
        if (!preg_match('/^[0-9]{3}$/', $cvv)) return false;

        return true;
    }
}