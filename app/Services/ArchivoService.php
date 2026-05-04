<?php

namespace App\Services;

use App\Models\ArchivoModel;

/**
 * servicio de gestion de archivos 
 */
class ArchivoService
{
    private const RUTA_UPLOADS = './uploads/archivos';
    private const MAX_TAMANIO = 20480000; // 20MB en bytes
    private const EXTENSIONES_PERMITIDAS = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'png', 'jpeg'];

    private ArchivoModel $archivoModel;

    public function __construct(ArchivoModel $archivoModel = null)
    {
        $this->archivoModel = $archivoModel ?? new ArchivoModel();
    }

    /**
     * se encarga de llamar al metodo validarArchivo y guardarArchivo para poder guardar el archivo subido
     */
    public function guardar($file)
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception('Archivo inválido o no proporcionado');
        }

        $this->validarArchivo($file);

        return $this->guardarArchivo($file);
    }

    /**
     * Valida el archivo subido antes de guardarlo
     *
     * @param object $file Archivo subido
     * @throws \Exception Si el archivo no cumple las reglas
     */
    private function validarArchivo($file): void
    {
        if ($file->getSize() > self::MAX_TAMANIO) {
            throw new \Exception('El archivo excede el tamaño máximo permitido (20MB)');
        }

        $extension = strtolower($file->getClientExtension());
        if (!in_array($extension, self::EXTENSIONES_PERMITIDAS)) {
            throw new \Exception('Tipo de archivo no permitido. Extensiones válidas: ' . implode(', ', self::EXTENSIONES_PERMITIDAS));
        }
    }

    /**
     * Guarda el archivo en el servidor y registra su metadata en la base de datos
     *
     * @param object $file Archivo subido
     * @return int ID del archivo guardado
     * @throws \Exception Si ocurre un error durante el guardado
     */
    private function guardarArchivo($file): int
    {
        $extension = strtolower($file->getClientExtension());
        $nombre = $file->getRandomName();

        if (!$file->move(self::RUTA_UPLOADS, $nombre)) {
            throw new \Exception('Error al guardar el archivo en el servidor');
        }

        $idArchivo = $this->archivoModel->insert([
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
     * @return array|null Datos del archivo
     */
    public function obtener($idArchivo)
    {
        return $this->archivoModel->find($idArchivo);
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

        if (file_exists($archivo['ruta'])) {
            unlink($archivo['ruta']);
        }

        return $this->archivoModel->delete($idArchivo);
    }
}
