<?php

namespace App\Services;

use App\Models\PublicacionModel;

/**
 * ═══════════════════════════════════════════════════════════════
 * SERVICIO: GESTIÓN DE PUBLICACIONES
 * ═══════════════════════════════════════════════════════════════
 * 
 * Responsable de:
 *   - CRUD de publicaciones
 *   - Procesamiento y validación de datos
 *   - Gestión de archivos adjuntos
 *   - Búsqueda y filtrado
 * 
 * Funcionalidades principales:
 *   - procesarPublicacion(): Crea o actualiza publicación
 *   - obtenerPublicacionesUsuario(): Lista de usuario
 *   - obtenerPublicacionPorId(): Detalle completo
 *   - actualizarPublicacion(): Actualización con validación
 *   - buscarPublicaciones(): Búsqueda con filtros
 * 
 * @author Sistema Univia
 * @package App\Services
 */
class PublicacionService
{
    private ArchivoService $archivoService;
    private PublicacionModel $publicacionModel;

    /**
     * Constructor del servicio
     * 
     * @param ArchivoService $archivoService Servicio de gestión de archivos
     * @param PublicacionModel|null $publicacionModel Modelo (opcional)
     */
    public function __construct(ArchivoService $archivoService, PublicacionModel $publicacionModel = null)
    {
        $this->archivoService = $archivoService;
        $this->publicacionModel = $publicacionModel ?? new PublicacionModel();
    }

    /**
     * Procesa una nueva publicación (creación)
     * 
     * Flujo:
     *   1. Valida datos de entrada
     *   2. Guarda archivo
     *   3. Construye objeto publicación
     *   4. Inserta en BD
     * 
     * @param array $datos Datos: titulo, descripcion, materia, tipo, tipo_acuerdo, precio, dni
     * @param object $archivo Archivo subido
     * @throws \Exception Si hay error en validación o guardado
     * @return void
     */
    public function procesarPublicacion(array $datos, $archivo)
    {
        $this->validarDatos($datos);

        if (!$archivo || !$archivo->isValid()) {
            throw new \Exception("El archivo es obligatorio para crear una publicación");
        }

        $idArchivo = $this->archivoService->guardar($archivo);

        $publicacion = $this->construirPublicacion($datos, $idArchivo);

        $this->guardarPublicacion($publicacion);
    }

    /**
     * Procesa un archivo sin procesar publicación completa
     * 
     * Utilizado en la edición de publicaciones
     * 
     * @param object $archivo Archivo subido
     * @return int ID del archivo guardado
     * @throws \Exception Si el archivo no es válido
     */
    public function procesarArchivo($archivo)
    {
        return $this->archivoService->guardar($archivo);
    }

        /**
     * Obtiene una publicación específica por su ID
     * 
     * Incluye información del archivo adjunto
     *
     * @param int $id ID de la publicación
     * @return array|null Array con datos completos o null si no existe
     */
    public function obtenerPublicacionPorId(int $id): ?array
    {
       $builder = $this->publicacionModel->builder();

        $builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta, a.formato');

        $builder->join('materia m', 'm.id_materia = publicacion.id_materia', 'left');
        
        $builder->join('archivo a', 'a.id_archivo = publicacion.id_archivo', 'left');

        $builder->where('publicacion.id_publicacion', $id);
        return $builder->get()->getRowArray();
    }

    /**
     * Obtiene todas las publicaciones de un usuario específico
     * 
     * Realiza joins para obtener información de materia y archivo
     *
     * @param string $dni DNI del usuario propietario
     * @param bool $soloActivas true = solo activas (estado=1), false = todas
     * @return array Array de publicaciones con datos completos
     */
    public function obtenerPublicacionesUsuario(string $dni, bool $soloActivas = true): array
    {
        $builder = $this->publicacionModel->builder();

        $builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta, a.formato');

        $builder->join('materia m', 'm.id_materia = publicacion.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = publicacion.id_archivo', 'left');

        $builder->where('publicacion.dni_usuario', $dni);

        if ($soloActivas) {
            $builder->where('publicacion.estado', 1);
        }

        $builder->orderBy('publicacion.fecha_publicacion', 'DESC');

        return $builder->get()->getResultArray();
    }


    /**
     * Valida todos los campos requeridos de una publicación
     * 
     * @param array $datos Datos a validar
     * @throws \Exception Si falta algún campo obligatorio
     */
    private function validarDatos(array $datos)
    {
        $camposObligatorios = ['titulo', 'descripcion', 'materia', 'tipo', 'tipo_acuerdo', 'dni'];

        foreach ($camposObligatorios as $campo) {
            if (empty($datos[$campo])) {
                throw new \Exception("El campo '{$campo}' es obligatorio");
            }
        }

        if ($datos['tipo_acuerdo'] === 'pago' && empty($datos['precio'])) {
            throw new \Exception("El precio es obligatorio para publicaciones de pago");
        }

        // Validar que tipo_acuerdo sea válido
        $tiposAcuerdoValidos = ['gratis', 'pago'];
        if (!in_array($datos['tipo_acuerdo'], $tiposAcuerdoValidos)) {
            throw new \Exception("El tipo de acuerdo seleccionado no es válido");
        }

        if (strlen($datos['titulo']) < 3) {
            throw new \Exception("El título debe tener al menos 3 caracteres");
        }
    }

    /**
     * Construye el array de datos para insertar en la base de datos
     * 
     * @param array $datos Datos de entrada
     * @param int $idArchivo ID del archivo
     * @return array Datos listos para guardar
     */
    private function construirPublicacion(array $datos, $idArchivo)
    {
        return [
            'titulo' => trim($datos['titulo']),
            'descripcion' => trim($datos['descripcion']),
            'id_materia' => (int)$datos['materia'],
            'tipo_recurso' => $datos['tipo'],
            'tipo_acuerdo' => $datos['tipo_acuerdo'],
            'precio' => !empty($datos['precio']) ? (float)$datos['precio'] : 0,
            'dni_usuario' => $datos['dni'],
            'id_archivo' => $idArchivo,
            'fecha_publicacion' => date('Y-m-d'),
            'estado' => 1,
        ];
    }

    /**
     * Guarda la publicación en la base de datos
     * 
     * @param array $data Datos de la publicación
     * @throws \Exception Si hay error en la inserción
     */
    private function guardarPublicacion(array $data)
    {
        $resultado = $this->publicacionModel->insert($data);

        if (!$resultado) {
            throw new \Exception("Error al guardar la publicación en la base de datos");
        }
    }

    /**
     * Actualiza los datos de una publicación existente
     * 
     * Si se actualiza el estado, se normaliza el valor (activo/1 o inactivo/0)
     *
     * @param int $id ID de la publicación
     * @param array $datos Campos a actualizar
     * @return bool true si se actualizó correctamente
     */
    public function actualizarPublicacion(int $id, array $datos): bool
    {
        if (array_key_exists('estado', $datos)) {
            $datos['estado'] = $this->normalizarEstado($datos['estado']);
        }

        return $this->publicacionModel->update($id, $datos);
    }

    /**
     * Normaliza el valor de estado a 1 (activo) o 0 (inactivo)
     *
     * @param mixed $estado Valor a normalizar (puede ser string, int, bool)
     * @return int 1 si activo, 0 si inactivo
     */
    private function normalizarEstado($estado): int
    {
        if ($estado === 'activo' || $estado === '1' || $estado === 1 || $estado === true) {
            return 1;
        }

        return 0;
    }

    /**
     * Marca una publicación como inactiva (no la elimina)
     *
     * @param int $id ID de la publicación
     * @return bool true si se actualizó correctamente
     */
    public function marcarPublicacionInactiva(int $id): bool
    {
        return $this->publicacionModel->update($id, ['estado' => 0]);
    }

}

