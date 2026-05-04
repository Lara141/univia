
<?php

namespace App\Services;

use App\Models\ArchivoModel;
use App\Models\PublicacionModel;

class PublicacionService
{
    public function procesarPublicacion(array $datos, $archivo)
    {
        $this->validar($datos, $archivo);

        $idArchivo = $this->guardarArchivo($archivo);

        $publicacion = $this->crearPublicacion($datos, $idArchivo);

        $this->guardar($publicacion);
    }

    private function validar(array $datos, $archivo)
    {
        if (empty($datos['titulo'])) {
            throw new \Exception("El título es obligatorio");
        }

        if (!$archivo || !$archivo->isValid()) {
            throw new \Exception("Archivo inválido");
        }
    }

    private function guardarArchivo($file)
    {
        $nombre = $file->getRandomName();
        $file->move('./uploads/archivos', $nombre);

        $archivoModel = new ArchivoModel();

        return $archivoModel->insert([
            'nombre_archivo' => $file->getClientName(),
            'ruta' => 'uploads/archivos/' . $nombre,
            'formato' => $file->getClientExtension(),
        ]);
    }

    private function crearPublicacion(array $datos, $idArchivo)
    {
        return [
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'id_materia' => $datos['materia'],
            'tipo_recurso' => $datos['tipo'],
            'tipo_acuerdo' => $datos['tipo_acuerdo'],
            'precio' => $datos['precio'],
            'dni_usuario' => $datos['dni'],
            'id_archivo' => $idArchivo,
            'fecha_publicacion' => date('Y-m-d'),
            'estado' => 1,
        ];
    }

    private function guardar(array $data)
    {
        $model = new PublicacionModel();
        $model->insert($data);
    }
}

