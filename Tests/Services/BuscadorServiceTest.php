<?php

namespace App\Tests\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\BuscadorService;
use App\Models\PublicacionModel;
use CodeIgniter\Database\BaseBuilder;

/**
 * Pruebas unitarias para BuscadorService.
 *
 * Esta clase prueba la lógica de búsqueda de publicaciones, asegurando que los filtros
 * se apliquen correctamente. Utiliza mocks para el modelo y el constructor de consultas
 * para no depender de una base de datos real.
 */
class BuscadorServiceTest extends CIUnitTestCase
{
    /**
     * Prueba la función de búsqueda de publicaciones con filtros.
     * Simula una cadena de llamadas al Query Builder y una respuesta de la base de datos
     * para verificar que el servicio procesa los filtros y devuelve los resultados esperados.
     */
    public function testBuscarPublicaciones()
    {
        // 1. Crear el mock del Builder
        $builderMock = $this->createMock(BaseBuilder::class);
        
        // Simular métodos encadenados del builder retornando la propia instancia
        $builderMock->method('select')->willReturnSelf();
        $builderMock->method('join')->willReturnSelf();
        $builderMock->method('where')->willReturnSelf();
        $builderMock->method('groupStart')->willReturnSelf();
        $builderMock->method('like')->willReturnSelf();
        $builderMock->method('orLike')->willReturnSelf();
        $builderMock->method('groupEnd')->willReturnSelf();
        $builderMock->method('orderBy')->willReturnSelf();

        // Datos simulados de respuesta
        $resultadosEsperados = [
            ['id_publicacion' => 1, 'titulo' => 'Física Básica'],
            ['id_publicacion' => 2, 'titulo' => 'Física Avanzada']
        ];

        // Simular el get() y getResultArray()
        $builderMock->method('get')->willReturn(
            new class($resultadosEsperados) {
                private $res;
                public function __construct($res) { $this->res = $res; }
                public function getResultArray() { return $this->res; }
            }
        );

        // 2. Crear mock del Modelo
        $publicacionModelMock = $this->createMock(PublicacionModel::class);
        $publicacionModelMock->method('builder')->willReturn($builderMock);

        // 3. Instanciar servicio inyectando el mock
        $buscadorService = new BuscadorService($publicacionModelMock);

        // Filtros de prueba
        $filtros = [
            'palabra_clave' => 'Física',
            'materia' => 5,
            'tipo' => 'resumen'
        ];

        // 4. Ejecutar
        $resultados = $buscadorService->buscarPublicaciones($filtros);

        // 5. Aserciones
        $this->assertIsArray($resultados);
        $this->assertCount(2, $resultados);
        $this->assertEquals('Física Básica', $resultados[0]['titulo']);
    }
}