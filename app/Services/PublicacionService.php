<?php

namespace App\Services;

use App\Models\PublicacionModel;

/**
 * Servicio de Publicaciones
 * Maneja la lógica de negocio para crear y actualizar publicaciones
 */
class PublicacionService
{
    private ArchivoService $archivoService;
    private PublicacionModel $publicacionModel;

    public function __construct(ArchivoService $archivoService, PublicacionModel $publicacionModel = null)
    {
        $this->archivoService = $archivoService;
        $this->publicacionModel = $publicacionModel ?? new PublicacionModel();
    }

    /**
     * Procesa una nueva publicación o actualización
     * 
     * @param array $datos Datos de la publicación
     * @param object $archivo Archivo subido
     * @throws \Exception Si hay error en validación o guardado
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
     * Procesa solamente un archivo (para ediciones)
     * 
     * @param object $archivo Archivo subido
     * @return int ID del archivo guardado
     */
    public function procesarArchivo($archivo)
    {
        return $this->archivoService->guardar($archivo);
    }

    /**
     * Obtiene las publicaciones de un usuario
     *
     * @param string $dni
     * @param bool $soloActivas
     * @return array
     */
    public function obtenerPublicacionesUsuario(string $dni, bool $soloActivas = true): array
    {
        $builder = $this->publicacionModel->builder();

$builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta');

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
     * Obtiene una publicación por su id
     *
     * @param int $id
     * @return array|null
     */
    public function obtenerPublicacionPorId(int $id): ?array
    {
       $builder = $this->publicacionModel->builder();

$builder->select('publicacion.*, a.nombre_archivo as file_name, a.ruta');

$builder->join('archivo a', 'a.id_archivo = publicacion.id_archivo', 'left');

$builder->where('publicacion.id_publicacion', $id);
        return $builder->get()->getRowArray();
    }

    /**
     * Obtiene publicaciones por materia
     *
     * @param int $idMateria
     * @return array
     */
    public function obtenerPublicacionesPorMateria(int $idMateria): array
    {
       $builder = $this->publicacionModel->builder();

$builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta');

$builder->join('materia m', 'm.id_materia = publicacion.id_materia', 'left');
$builder->join('archivo a', 'a.id_archivo = publicacion.id_archivo', 'left');

$builder->where('publicacion.id_materia', $idMateria);
$builder->where('publicacion.estado', 1);

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
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizarPublicacion(int $id, array $datos): bool
    {
        return $this->publicacionModel->update($id, $datos);
    }

    /**
     * Marca una publicación como inactiva
     *
     * @param int $id
     * @return bool
     */
    public function marcarPublicacionInactiva(int $id): bool
    {
        return $this->publicacionModel->update($id, ['estado' => 0]);
    }
}

