<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorar materiales | Univia</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #071739;
            color: white;
        }

        .navbar {
            background-color: #0B1F4D;
            padding: 15px 30px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #5DA9FF;
        }

        .usuario {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #7B61FF;

            display: flex;
            justify-content: center;
            align-items: center;

            font-weight: bold;
        }

        .contenedor {
            padding: 40px;
        }

        .titulo {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .subtitulo {
            color: #A0A8C0;
            margin-bottom: 40px;
        }

        .busqueda-container {
            display: flex;
            gap: 10px;
        }

        .input-busqueda {
            flex: 1;
            padding: 15px;
            border-radius: 10px;
            border: none;
            font-size: 16px;

            background-color: #101F4F;
            color: white;
        }

        .btn-buscar {
            padding: 15px 25px;
            border: none;
            border-radius: 10px;

            background-color: #2563EB;
            color: white;

            cursor: pointer;
            font-size: 16px;
        }

        .mensaje-vacio {
            margin-top: 80px;
            text-align: center;
            color: #A0A8C0;
            font-size: 20px;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="logo">Univia</div>

        <div class="usuario">
            <?= strtoupper(substr($usuario['nombre_usuario'] ?? 'U', 0, 1)) ?>
        </div>
    </div>

    <div class="contenedor">

        <div class="titulo">
            Explorar materiales
        </div>

        <div class="subtitulo">
            Busca apuntes, exámenes, resúmenes y recursos académicos.
        </div>

        <form action="<?= base_url('materiales/buscar') ?>" method="GET">

            <div class="busqueda-container">

                <input
                    type="text"
                    name="q"
                    class="input-busqueda"
                    placeholder="Buscar materiales..."
                >

                <button type="submit" class="btn-buscar">
                    Buscar
                </button>

            </div>

        </form>

        <div class="mensaje-vacio">
            Todavía no hay resultados para mostrar.
        </div>

    </div>

</body>
</html>