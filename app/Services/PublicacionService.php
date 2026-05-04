
<?php

namespace App\Services;

use App\Models\PublicacionModel;

/**
 * Servicio de Publicaciones
 * Maneja la lógica de negocio para crear y actualizar publicaciones
 */
class PublicacionService
{
    private $archivoService;

    public function __construct()
    {
        $this->archivoService = new ArchivoService();
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

        // Validación específica: si es tipo "pago", debe tener precio
        if ($datos['tipo_acuerdo'] === 'pago' && empty($datos['precio'])) {
            throw new \Exception("El precio es obligatorio para publicaciones de pago");
        }

        // Validar longitud mínima del título
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
        $model = new PublicacionModel();
        $resultado = $model->insert($data);

        if (!$resultado) {
            throw new \Exception("Error al guardar la publicación en la base de datos");
        }
    }
}

