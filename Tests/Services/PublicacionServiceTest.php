<?php

namespace App\Tests\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PublicacionService;
use App\Services\ArchivoService;
use App\Models\PublicacionModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\Query;

/**
 * Pruebas unitarias para PublicacionService.
 *
 * Estas pruebas verifican la lógica del servicio de publicaciones de forma aislada,
 * utilizando Mocks para simular las dependencias (ArchivoService, Base de Datos, Modelos)
 * y asegurar que el servicio se comporta como se espera sin interactuar con sistemas externos.
 */
class PublicacionServiceTest extends CIUnitTestCase
{
    private $archivoServiceMock;
    private $publicacionModelMock;
    private $dbMock;
    private $builderMock;
    private $publicacionService;

    /**
     * Configura el entorno de prueba antes de cada test.
     *
     * Inicializa los mocks para ArchivoService, PublicacionModel y la conexión a la base de datos.
     * Inyecta los mocks en una nueva instancia de PublicacionService para asegurar el aislamiento.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Mock de ArchivoService
        $this->archivoServiceMock = $this->createMock(ArchivoService::class);
        
        // 2. Mock del Modelo
        $this->publicacionModelMock = $this->createMock(PublicacionModel::class);
        
        // 3. Mock de la Base de datos y el Builder (para evitar inserciones reales)
        $this->dbMock = $this->createMock(BaseConnection::class);
        $this->builderMock = $this->createMock(BaseBuilder::class);
        
        // Inyectar el mock de la base de datos al servicio de CodeIgniter
        \Config\Services::injectMock('database', $this->dbMock);

        // Instanciar el servicio con los mocks inyectados
        $this->publicacionService = new PublicacionService(
            $this->archivoServiceMock, 
            $this->publicacionModelMock
        );

        // Usamos reflection para reemplazar la propiedad $db del servicio con nuestro mock
        $reflection = new \ReflectionClass($this->publicacionService);
        $dbProperty = $reflection->getProperty('db');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($this->publicacionService, $this->dbMock);
    }

    /**
     * Prueba el procesamiento exitoso de una publicación de tipo "físico".
     *
     * Verifica que el método `procesarPublicacion` pueda manejar correctamente
     * una publicación que no requiere la subida de un archivo, sin lanzar excepciones.
     */
    public function testProcesarPublicacionFisicaExitosa()
    {
        // Preparar datos de prueba (Libro físico, no requiere archivo)
        $datos = [
            'titulo' => 'Libro de Matemática',
            'descripcion' => 'Descripción de prueba',
            'materia' => 1,
            'tipo_recurso' => 'apunte',
            'tipo_acuerdo' => 'gratis',
            'precio' => 0,
            'formato_archivo' => 'fisico'
        ];
        $dniUsuario = '12345678';
        $archivoMock = null; 
        $imagenMock = null;

        // Simular que el tipo de recurso existe en la BD
        $rowTipoRecurso = (object) ['id_tipo_recurso' => 1];
        $this->builderMock->method('where')->willReturnSelf();
        $this->builderMock->method('get')->willReturn(
            new class($rowTipoRecurso) {
                private $row;
                public function __construct($row) { $this->row = $row; }
                public function getRow() { return $this->row; }
            }
        );

        // Configurar el mock de DB para que el table() devuelva el builderMock
        $this->dbMock->method('table')->willReturn($this->builderMock);
        
        // Simular que el insert es exitoso
        $this->builderMock->method('insert')->willReturn(true);

        // Ejecutar el método
        // Como no retorna nada, confiamos en que no lance excepciones
        $this->publicacionService->procesarPublicacion($datos, $archivoMock, $imagenMock, $dniUsuario);

        // Aserción: Si llegó hasta aquí sin lanzar excepción, la prueba pasó.
        $this->assertTrue(true);
    }

    /**
     * Prueba la funcionalidad de marcar una publicación como inactiva.
     *
     * Verifica que el método `marcarPublicacionInactiva` construye y ejecuta
     * correctamente la llamada al procedimiento almacenado `actualizar_estado_publicacion`
     * con los parámetros esperados (ID de publicación y estado 0).
     */
    public function testMarcarPublicacionInactiva()
    {
        $idPublicacion = 10;

        // Simular el resultado del Query para el procedimiento almacenado
        $queryMock = $this->createMock(Query::class);
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->with("CALL actualizar_estado_publicacion(?, ?)", [$idPublicacion, 0])
                     ->willReturn($queryMock); // Simula que la consulta fue exitosa

        // Ejecutar método
        $resultado = $this->publicacionService->marcarPublicacionInactiva($idPublicacion);

        // Verificar que retorne true
        $this->assertTrue($resultado);
    }
}