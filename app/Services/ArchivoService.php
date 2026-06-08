<?php

namespace App\Services;

use App\Models\ArchivoModel;

/**
 * ═══════════════════════════════════════════════════════════════
 * SERVICIO: GESTIÓN DE ARCHIVOS
 * ═══════════════════════════════════════════════════════════════
 * 
 * Responsable de:
 *   - Validar archivos subidos (tamaño, extensión)
 *   - Guardar archivos en el servidor
 *   - Registrar información de archivos en BD
 *   - Eliminar archivos (servidor y BD)
 * 
 * Validaciones:
 *   - Tamaño máximo: 20MB
 *   - Extensiones permitidas: pdf, doc, docx, txt, xls, xlsx, ppt, pptx, zip, jpg, png, jpeg
 * 
 * @author Sistema Univia
 * @package App\Services
 */
class ArchivoService
{  
    private const RUTA_UPLOADS = './uploads/archivos';
    private const MAX_TAMANIO = 20480000; // 20MB en bytes
    private const EXTENSIONES_PERMITIDAS = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'png', 'jpeg'];

    private ArchivoModel $archivoModel;

    /**
     * Constructor del servicio
     * 
     * @param ArchivoModel|null $archivoModel Instancia del modelo (opcional)
     */
    public function __construct(ArchivoModel $archivoModel = null)
    {
        $this->archivoModel = $archivoModel ?? new ArchivoModel();
    }

    /**
     * Valida y guarda un archivo subido
     * 
     * Proceso:
     *   1. Valida que el archivo sea válido
     *   2. Valida tamaño y extensión
     *   3. Guarda en servidor y registra en BD
     * 
     * @param object $file Archivo subido (de $_FILES)
     * @return int ID del archivo guardado
     * @throws \Exception Si el archivo no cumple las reglas
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
     * Valida el archivo antes de guardarlo
     * 
     * Validaciones:
     *   - Tamaño no puede exceder 20MB
     *   - Extensión debe estar en lista permitida
     *
     * @param object $file Archivo subido
     * @throws \Exception Si el archivo no cumple las reglas
     * @return void
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
     * Guarda el archivo en el servidor y registra metadata en BD
     * 
     * Proceso:
     *   1. Genera nombre aleatorio para el archivo
     *   2. Mueve archivo al directorio de uploads
     *   3. Registra información en tabla archivo
     * 
     * @param object $file Archivo subido
     * @return int ID del archivo en la BD
     * @throws \Exception Si ocurre error durante el guardado
     */
    private function guardarArchivo($file): int
    {
        $extension = strtolower($file->getClientExtension());
        $nombre = $file->getRandomName();

        if (!$file->move(self::RUTA_UPLOADS, $nombre)) {
            throw new \Exception('Error al guardar el archivo en el servidor');
        }

        $db = \Config\Database::connect();
        $formatoRow = $db->table('formato')->where('slug', $extension)->get()->getRow();
        $idFormato = $formatoRow ? (int)$formatoRow->id_formato : null;

        $idArchivo = $this->archivoModel->insert([
            'nombre_archivo' => $file->getClientName(),
            'ruta'           => self::RUTA_UPLOADS . '/' . $nombre,
            'id_formato'     => $idFormato,
        ]);

        if (!$idArchivo) {
            throw new \Exception('Error al guardar la información del archivo en la base de datos');
        }

        return $idArchivo;
    }

    /**
     * Obtiene información de un archivo por su ID
     * 
     * @param int $idArchivo ID del archivo
     * @return array|null Datos del archivo o null si no existe
     */
    public function obtener($idArchivo)
    {
        return $this->archivoModel->find($idArchivo);
    }

    /**
     * Elimina un archivo del servidor y registra en BD
     * 
     * @param int $idArchivo ID del archivo a eliminar
     * @return bool true si se eliminó correctamente
     * @throws \Exception Si el archivo no existe
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
