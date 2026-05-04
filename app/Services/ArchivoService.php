<?php

namespace App\Services;

use App\Models\ArchivoModel;

/**
 * Servicio de Gestión de Archivos
 * Centraliza la lógica de guardar, validar y gestionar archivos
 */
class ArchivoService
{
    private const RUTA_UPLOADS = './uploads/archivos';
    private const MAX_TAMANIO = 20480000; // 20MB en bytes
    private const EXTENSIONES_PERMITIDAS = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'png', 'jpeg'];

    /**
     * Guarda un archivo en el servidor
     * 
     * @param object $file Archivo subido
     * @return int ID del archivo guardado
     * @throws \Exception Si el archivo es inválido
     */
    public function guardar($file)
    {
        // Validar que el archivo exista y sea válido
        if (!$file || !$file->isValid()) {
            throw new \Exception('Archivo inválido o no proporcionado');
        }

        // Validar tamaño
        if ($file->getSize() > self::MAX_TAMANIO) {
            throw new \Exception('El archivo excede el tamaño máximo permitido (20MB)');
        }

        // Validar extensión
        $extension = strtolower($file->getClientExtension());
        if (!in_array($extension, self::EXTENSIONES_PERMITIDAS)) {
            throw new \Exception('Tipo de archivo no permitido. Extensiones válidas: ' . implode(', ', self::EXTENSIONES_PERMITIDAS));
        }

        // Generar nombre único para el archivo
        $nombre = $file->getRandomName();
        
        // Mover archivo a la carpeta de uploads
        if (!$file->move(self::RUTA_UPLOADS, $nombre)) {
            throw new \Exception('Error al guardar el archivo en el servidor');
        }

        // Guardar referencia en la base de datos
        $archivoModel = new ArchivoModel();
        $idArchivo = $archivoModel->insert([
            'nombre_archivo' => $file->getClientName(),
            'ruta' => self::RUTA_UPLOADS . '/' . $nombre,
            'formato' => $extension,
        ]);

        if (!$idArchivo) {
            throw new \Exception('Error al guardar la información del archivo en la base de datos');
        }

        return $idArchivo;
    }

    /**
     * Obtiene información de un archivo
     * 
     * @param int $idArchivo ID del archivo
     * @return array Datos del archivo
     */
    public function obtener($idArchivo)
    {
        $archivoModel = new ArchivoModel();
        return $archivoModel->find($idArchivo);
    }

    /**
     * Elimina un archivo del servidor y la base de datos
     * 
     * @param int $idArchivo ID del archivo a eliminar
     * @return bool
     */
    public function eliminar($idArchivo)
    {
        $archivo = $this->obtener($idArchivo);
        
        if (!$archivo) {
            throw new \Exception('Archivo no encontrado');
        }

        // Eliminar archivo del servidor
        if (file_exists($archivo['ruta'])) {
            unlink($archivo['ruta']);
        }

        // Eliminar de la base de datos
        $archivoModel = new ArchivoModel();
        return $archivoModel->delete($idArchivo);
    }
}
