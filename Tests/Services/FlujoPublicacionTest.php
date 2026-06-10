<?php

namespace App\Tests\Services;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Services\PublicacionService;
use App\Services\ArchivoService;
use App\Services\PagoService;
use App\Services\BuscadorService;
use App\Models\PublicacionModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * @internal
 */
class FlujoPublicacionTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait; // Útil para helpers como seeInDatabase()

    // Si tienes migraciones, descomenta la siguiente línea.
    // protected $migrate = true;

    // --- Models ---
    protected PublicacionModel $publicacionModel;
    protected UsuarioModel $usuarioModel;

    // --- Services under test ---
    protected PublicacionService $publicacionService;
    protected BuscadorService $buscadorService;
    protected PagoService $pagoService;

    // --- Mocks ---
    protected $mockArchivoService;

    public function setUp(): void
    {
        parent::setUp();

        // 1. Initialize all models
        $this->publicacionModel = new PublicacionModel();
        $this->usuarioModel = new UsuarioModel();

        // 2. Create mocks for external dependencies (like file system or external APIs)
        $this->mockArchivoService = $this->createMock(ArchivoService::class);

        // 3. Initialize services under test, injecting real models and mocks.
        $this->publicacionService = new PublicacionService($this->mockArchivoService, $this->publicacionModel);
        $this->buscadorService = new BuscadorService($this->publicacionModel);
        $this->pagoService = new PagoService(); 

        // 4. Seed the database with common data for all tests
        $this->seedDatabase();
    }

    /**
     * Helper method to seed the database with initial data.
     * This runs before each test and is rolled back automatically.
     */
    protected function seedDatabase(): void
    {
        $this->usuarioModel->insert([
            'dni_usuario'      => '99999999',
            'Nombre_usuario'   => 'Tester',
            'Apellido_usuario' => 'Pruebas',
            'correo'           => 'tester@univia.com',
            'contrasena'       => password_hash('testing123', PASSWORD_DEFAULT),
        ]);

        $this->db->table('materia')->insert(['id_materia' => 99, 'nombre_materia' => 'Materia de Prueba']);
        $this->db->table('tipo_recurso')->insert(['id_tipo_recurso' => 99, 'slug' => 'resumen-test', 'nombre_tipo' => 'Resumen de Prueba']);
        $this->db->table('formato')->insert(['id_formato' => 99, 'slug' => 'pdf-test', 'nombre_formato' => 'PDF de Prueba']);
    }

    public function testProcesarPublicacion()
    {
        // 1. ARRANGE
        $this->mockArchivoService->method('guardar')->willReturn(12345);

        $datosPublicacion = [
            'titulo' => 'Mi Publicación de Prueba',
            'descripcion' => 'Descripción de prueba.',
            'materia'      => 99, // Usamos el ID de la materia de prueba
            'tipo_recurso' => 'resumen-test',
            'tipo_acuerdo' => 'gratis',
            'precio'       => 0,
            'formato_archivo' => 'pdf-test',
        ];

        $mockArchivo = $this->createMock(UploadedFile::class);
        $mockArchivo->method('isValid')->willReturn(true);
        $mockArchivo->method('getExtension')->willReturn('pdf');

        // 2. ACT
        $this->publicacionService->procesarPublicacion($datosPublicacion, $mockArchivo, null, '99999999');

        // 3. ASSERT
        $this->seeInDatabase('publicacion', [
            'titulo' => 'Mi Publicación de Prueba',
            'dni_usuario' => '99999999',
            'id_archivo' => 12345
        ]);
    }

    /**
     * Prueba la obtención de publicaciones de un usuario.
     */
    public function testObtenerPublicacionesUsuario()
    {
        // 1. ARRANGE
        // CORRECCIÓN: Se añade el campo 'fecha_publicacion' que es requerido por la BD.
        $this->publicacionModel->insertBatch([
            ['titulo' => 'Pub 1', 'dni_usuario' => '99999999', 'id_materia' => 99, 'id_tipo_recurso' => 99, 'tipo_acuerdo' => 'gratis', 'estado' => 1, 'fecha_publicacion' => date('Y-m-d')],
            ['titulo' => 'Pub 2 Inactiva', 'dni_usuario' => '99999999', 'id_materia' => 99, 'id_tipo_recurso' => 99, 'tipo_acuerdo' => 'gratis', 'estado' => 0, 'fecha_publicacion' => date('Y-m-d')],
        ]);

        // 2. ACT & ASSERT
        // NOTA: Se prueba directamente el método del Modelo (`publicacionModel`) en lugar del
        // Servicio (`publicacionService`). Esto se debe a que el método del servicio utiliza
        // un Stored Procedure (`obtener_publicaciones_usuario`), que no está disponible
        // en el entorno de pruebas transaccional. El método del modelo usa el Query Builder,
        // que es 100% compatible con las pruebas y logra el mismo resultado.
        // Caso 1: Solo activas
        $activas = $this->publicacionService->obtenerPublicacionesUsuario('99999999', true);
        $this->assertCount(1, $activas);
        $this->assertEquals('Pub 1', $activas[0]['titulo']);
 
        // Caso 2: Todas (activas e inactivas)
        $todas = $this->publicacionService->obtenerPublicacionesUsuario('99999999', false);
        $this->assertCount(2, $todas);
    }

    public function testBuscarPublicaciones()
    {
        // 1. ARRANGE
        // CORRECCIÓN: Se añade el campo 'fecha_publicacion' que es requerido por la BD.
        $this->publicacionModel->insert([
            'titulo' => 'Apunte de Algoritmos',
            'descripcion' => 'Un resumen completo.',
            'dni_usuario' => '99999999',
            'id_materia' => 99,
            'id_tipo_recurso' => 99,
            'tipo_acuerdo' => 'gratis',
            'estado' => 1,
            'fecha_publicacion' => date('Y-m-d'),
            'id_archivo' => null
        ]);

        // 2. ACT
        $resultados = $this->buscadorService->buscarPublicaciones(['palabra_clave' => 'Algoritmos']);

        // 3. ASSERT
        $this->assertCount(1, $resultados);
        $this->assertEquals('Apunte de Algoritmos', $resultados[0]['titulo']);

        // Probamos un filtro que no debe encontrar nada
        $resultadosVacios = $this->buscadorService->buscarPublicaciones(['palabra_clave' => 'Inexistente']);
        $this->assertCount(0, $resultadosVacios);
    }

    public function testRegistrarNuevoPago()
    {
        // 1. ARRANGE
        // CORRECCIÓN: Se inserta una publicación real para obtener un ID válido
        // y evitar errores de restricción de clave foránea en la tabla 'pago'.
        $idPublicacionPrueba = $this->publicacionModel->insert([
            'titulo' => 'Material Premium de Prueba',
            'descripcion' => 'Descripción',
            'dni_usuario' => '99999999',
            'id_materia' => 99,
            'id_tipo_recurso' => 99,
            'tipo_acuerdo' => 'pago', 'precio' => 500.00, 'estado' => 1, 'fecha_publicacion' => date('Y-m-d'), 'id_archivo' => null
        ]);

        // 2. ACT
        $resultado = $this->pagoService->registrarNuevoPago('99999999', (int) $idPublicacionPrueba, 500.00);

        // 3. ASSERT
        $this->assertTrue($resultado);
        $this->seeInDatabase('pago', [
            'dni_usuario' => '99999999',
            'id_publicacion' => $idPublicacionPrueba
        ]);
    }

    public function testActualizarPublicacion()
    {
        // 1. ARRANGE
        // CORRECCIÓN: Se añade el campo 'fecha_publicacion' que es requerido por la BD.
        $id = $this->publicacionModel->insert([
            'titulo' => 'Título Original', 
            'dni_usuario' => '99999999', 
            'id_materia' => 99, 
            'id_tipo_recurso' => 99, 
            'tipo_acuerdo' => 'gratis', 
            'estado' => 1, 'fecha_publicacion' => date('Y-m-d'), 'id_archivo' => null
        ]);

        $datosUpdate = [
            'titulo' => 'Título Actualizado',
            'precio' => 150.50
        ];

        // 2. ACT
        // NOTA: Se prueba directamente el método del Servicio, ya que este usa un
        // Stored Procedure (`actualizar_publicacion`) que es parte de la lógica a validar.
        // Si el SP no existiera en la BD de pruebas, este test fallaría.
        $resultado = $this->publicacionService->actualizarPublicacion((int) $id, $datosUpdate);

        // 3. ASSERT
        $this->assertTrue($resultado);
        $this->seeInDatabase('publicacion', [ 
            'id_publicacion' => $id,
            'titulo' => 'Título Actualizado',
            'precio' => 150.50
        ]);
    }

    public function testMarcarPublicacionInactiva()
    {
        // 1. ARRANGE
        // CORRECCIÓN: Se añade el campo 'fecha_publicacion' que es requerido por la BD.
        $id = $this->publicacionModel->insert([
            'titulo' => 'Publicación a Desactivar', 
            'dni_usuario' => '99999999', 
            'id_materia' => 99, 
            'id_tipo_recurso' => 99, 
            'tipo_acuerdo' => 'gratis', 
            'estado' => 1, 'fecha_publicacion' => date('Y-m-d'), 'id_archivo' => null
        ]);

        // 2. ACT
        // NOTA: Se prueba directamente el método del Servicio, ya que este usa un
        // Stored Procedure (`actualizar_estado_publicacion`) que es parte de la lógica a validar.
        // Si el SP no existiera en la BD de pruebas, este test fallaría.
        $resultado = $this->publicacionService->marcarPublicacionInactiva((int) $id);

        // 3. ASSERT
        $this->assertTrue($resultado);
        $this->seeInDatabase('publicacion', [
            'id_publicacion' => $id,
            'estado' => 0
        ]);
        $this->dontSeeInDatabase('publicacion', [
            'id_publicacion' => $id,
            'estado' => 1
        ]);
    }
}