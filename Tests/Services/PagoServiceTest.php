<?php

namespace App\Tests\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\PagoService;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\BaseBuilder;

/**
 * Pruebas unitarias para PagoService.
 *
 * Se enfoca en verificar la lógica de negocio del servicio de pagos de forma aislada,
 * utilizando Mocks para simular la interacción con la base de datos.
 */
class PagoServiceTest extends CIUnitTestCase
{
    private $dbMock;
    private $builderMock;
    private $pagoService;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Configurar Mocks de BD
        $this->dbMock = $this->createMock(BaseConnection::class);
        $this->builderMock = $this->createMock(BaseBuilder::class);
        
        // Simular que el método table() devuelve nuestro builder
        $this->dbMock->method('table')->willReturn($this->builderMock);

        // 2. Instanciar el servicio
        $this->pagoService = new PagoService();

        // 3. Inyectar el Mock usando Reflection (sobreescribimos la propiedad $db protegida)
        $reflection = new \ReflectionClass($this->pagoService);
        if ($reflection->hasProperty('db')) {
            $dbProperty = $reflection->getProperty('db');
            $dbProperty->setAccessible(true);
            $dbProperty->setValue($this->pagoService, $this->dbMock);
        }
    }

    /**
     * Prueba el registro exitoso de un nuevo pago.
     *
     * Verifica que el método `registrarNuevoPago` llama al método `insert` del
     * constructor de consultas con los datos correctos, incluyendo la fecha actual.
     * Simula una inserción exitosa y afirma que el método devuelve `true`.
     */
    public function testRegistrarNuevoPago()
    {
        // Datos de prueba
        $dni = '12345678';
        $idPublicacion = 99;
        $monto = 500.50;

        // Esperamos que insert() reciba un arreglo con las claves específicas
        $this->builderMock->expects($this->once())
             ->method('insert')
             ->with($this->callback(function($datosInsertados) use ($dni, $idPublicacion, $monto) {
                 return $datosInsertados['dni_usuario'] === $dni &&
                        $datosInsertados['id_publicacion'] === $idPublicacion &&
                        $datosInsertados['monto'] === $monto &&
                        isset($datosInsertados['fecha_pago']);
             }))
             ->willReturn(true); // Simula inserción exitosa

        // Ejecutar método
        $resultado = $this->pagoService->registrarNuevoPago($dni, $idPublicacion, $monto);

        // Verificamos
        $this->assertTrue($resultado);
    }
}