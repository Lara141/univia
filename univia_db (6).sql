-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-06-2026 a las 15:00:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `univia_db`
-- 

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_estado_publicacion` (IN `in_id_publicacion` INT, IN `in_nuevo_estado` TINYINT)   BEGIN
    -- Actualiza el campo 'estado' de una publicación específica.
    UPDATE
        publicacion
    SET
        estado = in_nuevo_estado
    WHERE
        id_publicacion = in_id_publicacion;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_publicaciones_usuario` (IN `in_dni_usuario` INT, IN `in_solo_activas` BOOLEAN)   BEGIN
    -- Selecciona las publicaciones de un usuario específico.
    -- Realiza un JOIN con las tablas 'materia', 'archivo' y 'formato' para obtener datos completos.
    SELECT
        p.*,
        m.nombre_materia,
        a.nombre_archivo AS file_name,
        a.ruta,
        f.nombre_formato AS formato, -- Traemos el nombre del formato desde la tabla formato
        f.icono AS icono_formato
    FROM
        publicacion p
    LEFT JOIN
        materia m ON m.id_materia = p.id_materia
    LEFT JOIN
        archivo a ON a.id_archivo = p.id_archivo
    LEFT JOIN 
        formato f ON a.id_formato = f.id_formato -- Nuevo JOIN para solucionar el error
    WHERE
        p.dni_usuario = in_dni_usuario
        AND (in_solo_activas = 0 OR p.estado = 1)
    ORDER BY
        p.fecha_publicacion DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivo`
--

CREATE TABLE `archivo` (
  `id_archivo` int(11) NOT NULL,
  `nombre_archivo` varchar(80) NOT NULL,
  `ruta` varchar(100) NOT NULL,
  `id_formato` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `archivo`
--

INSERT INTO `archivo` (`id_archivo`, `nombre_archivo`, `ruta`, `id_formato`) VALUES
(11, 'GUIA DE TP N° 0 (1).pdf', './uploads/archivos/1778182424_48603281070032f89ffa.pdf', 1),
(12, 'Economía Aplicada 2026.pdf', './uploads/archivos/1778182635_444221bdef8a48a5a4d0.pdf', 1),
(13, 'Economía Aplicada 2026.pdf', './uploads/archivos/1778182672_924a828cb2159aa56005.pdf', 1),
(14, 'Mesa Regular 18-12-25.pdf', './uploads/archivos/1778182885_bc1766fe9000e47b5ebc.pdf', 1),
(15, 'Mesa Regular 18-12-25.pdf', './uploads/archivos/1778182942_af963ffed2a1ec9f25a4.pdf', 1),
(16, 'Economía Aplicada 2026.pdf', './uploads/archivos/1780089074_dc784b8ac15093c01072.pdf', 1),
(17, 'modelo_examenes_algebra.docx', './uploads/archivos/1780089241_e801f16b19ad02201cad.docx', 2),
(18, 'libro_matematica2.jpeg', './uploads/archivos/1780091148_64cdb09a25ff2921e43e.jpeg', 5),
(19, 'Teoría de la Computación.docx', './uploads/archivos/1780260951_2d69c4848e23b7061fae.docx', 2),
(20, 'Estudio_y_Analisis_Competencia_MateOs.pdf', './uploads/archivos/1780262390_9d3e471645c1dba387b0.pdf', 1),
(21, 'arqui_machetito.pdf', './uploads/archivos/1780262473_09a6b39ffa8b9eb7830c.pdf', 1),
(22, 'Entrega primer avance Grupo 64.pdf', './uploads/archivos/1780262553_b343698d056f8960c285.pdf', 1),
(23, 'formato ieee.pdf', './uploads/archivos/1780274133_ac8647c46f521dd20224.pdf', 1),
(24, 'Presentacion_Optimización_Índices.pptx', './uploads/archivos/1780274276_e2b431c93dd2357c889e.pptx', 4),
(25, 'Resumen_Tema3_Ampliado_IS_Valentina.docx', './uploads/archivos/1780274672_4a6ee5d6d28c858edff4.bin', 2),
(26, 'Base de Datos I - GIT.pptx', './uploads/archivos/1780274851_ff7e853a1ba3e8d41b38.pptx', 4),
(27, 'Actividades 1 y 2 del MÓDULO 2 (22-04-26).docx', './uploads/archivos/1780275106_f8604f041bfdd8dbfec0.docx', 2),
(28, 'Univia_Recuperatorio_TDC_AcostaLara-BenitezValentina.pdf', './uploads/archivos/1780276498_113c91813e9e9ad90b65.pdf', 1),
(29, 'Mi_Investigación_VB (1).pdf', './uploads/archivos/1780544452_1336d56ac02ce8d58003.pdf', 1),
(30, 'DS_FINAL.drawio (1).png', './uploads/archivos/1780547077_dd6f9cb566bf9c57dbaf.png', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera`
--

CREATE TABLE `carrera` (
  `id_carrera` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `id_facultad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrera`
--

INSERT INTO `carrera` (`id_carrera`, `nombre`, `id_facultad`) VALUES
(1, 'Licenciatura en Sistemas de la Información', 1),
(2, 'Ingeniería en Electrónica', 1),
(3, 'Profesorado en Matemáticas', 1),
(4, 'Abogacía', 2),
(5, 'Notariado', 2),
(6, 'Ingeniería en Sistemas de Información', 4),
(7, 'Ingeniería Electromecánica', 4),
(8, 'Tecnicatura Universitaria en Programación', 4),
(9, 'Ingeniería Informática', 6),
(10, 'Ingeniería Industrial', 6),
(11, 'Ingeniería Civil', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera_materia`
--

CREATE TABLE `carrera_materia` (
  `id_carrera_materia` int(11) NOT NULL,
  `anio_cursado` date DEFAULT NULL,
  `id_carrera` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrera_materia`
--

INSERT INTO `carrera_materia` (`id_carrera_materia`, `anio_cursado`, `id_carrera`, `id_materia`) VALUES
(1, NULL, 1, 1),
(2, NULL, 1, 2),
(3, NULL, 1, 3),
(4, NULL, 1, 4),
(5, NULL, 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facultad`
--

CREATE TABLE `facultad` (
  `id_facultad` int(11) NOT NULL,
  `nombre_facultad` varchar(80) NOT NULL,
  `id_universidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facultad`
--

INSERT INTO `facultad` (`id_facultad`, `nombre_facultad`, `id_universidad`) VALUES
(1, 'FaCENA', 1),
(2, 'Facultad de Derecho y Ciencias Sociales y Políticas', 1),
(3, 'Facultad de Medicina', 1),
(4, 'Facultad Regional Resistencia (FRRe)', 2),
(5, 'Facultad Regional Buenos Aires (FRBA)', 2),
(6, 'Facultad de Ingeniería (FIUBA)', 3),
(7, 'Facultad de Ciencias Económicas (FCE)', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formato`
--

CREATE TABLE `formato` (
  `id_formato` int(11) NOT NULL,
  `nombre_formato` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT NULL COMMENT 'Ej: bi-file-earmark-pdf'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `formato`
--

INSERT INTO `formato` (`id_formato`, `nombre_formato`, `slug`, `icono`) VALUES
(1, 'PDF', 'pdf', 'bi-file-earmark-pdf'),
(2, 'Word (.doc / .docx)', 'word', 'bi-file-earmark-word'),
(3, 'Excel (.xls / .xlsx)', 'excel', 'bi-file-earmark-excel'),
(4, 'PowerPoint (.ppt / .pptx)', 'powerpoint', 'bi-file-earmark-slides'),
(5, 'Imagen (JPG / PNG)', 'imagen', 'bi-image'),
(6, 'Libro / Material físico', 'fisico', 'bi-book-half'),
(7, 'Otro', 'otro', 'bi-box');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia`
--

CREATE TABLE `materia` (
  `id_materia` int(11) NOT NULL,
  `nombre_materia` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materia`
--

INSERT INTO `materia` (`id_materia`, `nombre_materia`) VALUES
(1, 'Ingenieria de software 2'),
(2, 'Probabilidad y estadistica'),
(3, 'Base de datos 1'),
(4, 'Ingeniería de software 2'),
(5, 'Economia aplicada'),
(6, 'Comunicación Global y Estratégica'),
(7, 'Negocios y Marketing Internacional'),
(8, 'Neurociencias'),
(9, 'Psicologia general');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `dni_usuario` int(8) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id_pago`, `dni_usuario`, `id_publicacion`, `fecha_pago`, `monto`) VALUES
(1, 10200300, 11, '2026-06-04', 50.01),
(2, 10200300, 16, '2026-06-04', 2000),
(3, 10200300, 15, '2026-06-04', 6000),
(4, 10200300, 26, '2026-06-04', 3999.99),
(5, 90800700, 27, '2026-06-04', 500),
(6, 90800700, 15, '2026-06-04', 6000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id_publicacion` int(11) NOT NULL,
  `titulo` varchar(80) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `id_tipo_recurso` int(11) NOT NULL,
  `tipo_acuerdo` varchar(13) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `dni_usuario` int(8) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_archivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`id_publicacion`, `titulo`, `descripcion`, `id_tipo_recurso`, `tipo_acuerdo`, `precio`, `fecha_publicacion`, `estado`, `dni_usuario`, `id_materia`, `id_archivo`) VALUES
(10, 'Practico N°0', 'Es el practico N°o de la materia de ingenieria 2, solo contiene un ejercicio practico sin resolver', 4, 'gratis', 0.00, '2026-05-07', 1, 22333444, 4, 11),
(11, 'Modulo 1 de teoria', 'Contiene el módulo 1 completo de teoría con ejemplos prácticos', 5, 'pago', 50.01, '2026-05-07', 1, 22333444, 5, 13),
(12, 'Modelo de examen final', 'Es un modelo de mesa de examen regular del año 2025', 3, 'gratis', 0.00, '2026-05-07', 0, 22333444, 3, 15),
(13, 'Resumen de Economia Aplicada 2026', 'Este resumen lo hice cuando curse la materia, contiene un resumen de cada uno de los temas dados.', 1, 'gratis', 0.00, '2026-05-29', 0, 10200300, 5, 16),
(14, 'Algebra y geometría analitica 2020', 'Estos examenes son del año 2020, te serviran para modelos y prácticar.', 3, 'gratis', 0.00, '2026-05-29', 1, 10200300, 3, 17),
(15, 'Analsisi matematico II', 'Este libro contiene toda la información de la materia, de donde sacan los pdf para los temas abordados.', 2, 'pago', 6000.00, '2026-05-29', 1, 10200300, 2, 18),
(16, 'Examenes de ingenieria 2', 'estos modelos de examenes de ingenieria 2 corresponden al primer parcial del año 2021', 3, 'pago', 2000.00, '2026-05-31', 1, 10200300, 4, 19),
(20, 'Guia de ejercicios de Probabilidad 2022', 'contiene la guia de tp resueltos', 4, 'gratis', 0.00, '2026-06-01', 1, 10200300, 2, 23),
(21, 'Presentación de optimización de Ingenieria 2', 'incluye la presentacion de la clase', 5, 'gratis', 0.00, '2026-06-01', 1, 10200300, 4, 24),
(22, 'Resumen mio de Programacion 1', 'En este resumen se encuentran todas las unidades del primer cuatrimestre', 1, 'gratis', 0.00, '2026-06-01', 1, 90800700, 1, 25),
(23, 'Base de datos 1 - Tema 4 al 9', 'En este material vas a encontrar los temas de bdd 1, correspondientes al segundo parcial. Del año 2018', 5, 'gratis', 0.00, '2026-06-01', 1, 90800700, 1, 26),
(25, 'Apunte de la materia neurociencias cursada en el año 2020', 'Este apunte contiene los temas abordados en neurociencias, del tema 1 al 10. En el año 2020', 5, 'gratis', 0.00, '2026-06-01', 1, 90800700, 8, 28),
(26, 'Proyecto integardor de economia', 'En este documento vana  encontrar nuestro proyecto integrador con la caul aprobamos la materia econpomia aplicada el año 2026. Tambien ganamos el 2do puesto del hackaton', 5, 'pago', 3999.99, '2026-06-04', 1, 10200300, 5, 29),
(27, 'Diagrama de secuencia de ejemplo', 'Este diagrama de secuencia les ayudara a saber que quieren lso profes que hagan', 5, 'pago', 500.00, '2026-06-04', 1, 90800700, 4, 30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_recurso`
--

CREATE TABLE `tipo_recurso` (
  `id_tipo_recurso` int(11) NOT NULL,
  `nombre_tipo` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT NULL COMMENT 'Ej: bi-file-earmark-text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_recurso`
--

INSERT INTO `tipo_recurso` (`id_tipo_recurso`, `nombre_tipo`, `slug`, `icono`) VALUES
(1, 'Resumen', 'resumen', 'bi-file-earmark-text'),
(2, 'Libro', 'libro', 'bi-book'),
(3, 'Examen / Parcial', 'examen', 'bi-clipboard-check'),
(4, 'Guía de ejercicios', 'guia', 'bi-journal-bookmark'),
(5, 'Otro', 'otro', 'bi-folder');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `universidad`
--

CREATE TABLE `universidad` (
  `id_universidad` int(11) NOT NULL,
  `nombre_universidad` varchar(200) NOT NULL,
  `slug` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `universidad`
--

INSERT INTO `universidad` (`id_universidad`, `nombre_universidad`, `slug`) VALUES
(1, 'UNNE', 'unne'),
(2, 'Universidad Tecnológica Nacional (UTN)', 'utn'),
(3, 'Universidad de Buenos Aires (UBA)', 'uba');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `dni_usuario` int(8) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `contrasena` varchar(200) NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `apellido_usuario` varchar(50) NOT NULL,
  `id_carrera` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`dni_usuario`, `correo`, `contrasena`, `fecha_registro`, `estado`, `nombre_usuario`, `apellido_usuario`, `id_carrera`) VALUES
(10200300, 'valenbz1@gmail.com', '$2y$10$8J78ceqEWE9EAM0GEKqIROLE8qMwusqfOtct4HWq4EgMI9FzwS.lW', '2026-05-29', 1, 'Valentina Itatí', 'Benitez', 1),
(22333444, 'diamelaacosta2004@gmail.com', '$2y$10$wrgpwd1YKBtcPkCC9ZFiXe18vfDeCKTqGrhn6EnyR2Y4/RsTcagnS', '2026-05-07', 1, 'Lara', 'Acosta', 1),
(90800700, 'tomibolo1@gmail.com', '$2y$10$i480fDmiV7scqG3IbevQy.1QY8nMqV4HSwSd/.eARKYidAfRSdnKC', '2026-05-31', 1, 'Tomas Andres', 'Bolo', 1),
(99888777, 'valenitati01@gmail.com', '$2y$10$frRp3dKtbDLK04qt4QOHDu2Y0wBzJOReB7AD/NkAnQu3YMPmRgRCi', '2026-05-29', 1, 'Valentina Itatí', 'Benitez', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivo`
--
ALTER TABLE `archivo`
  ADD PRIMARY KEY (`id_archivo`),
  ADD KEY `fk_archivo_formato` (`id_formato`);

--
-- Indices de la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD PRIMARY KEY (`id_carrera`),
  ADD KEY `fk_carrera_facultad` (`id_facultad`);

--
-- Indices de la tabla `carrera_materia`
--
ALTER TABLE `carrera_materia`
  ADD PRIMARY KEY (`id_carrera_materia`),
  ADD KEY `fk_cm_carrera` (`id_carrera`),
  ADD KEY `fk_cm_materia` (`id_materia`);

--
-- Indices de la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD PRIMARY KEY (`id_facultad`),
  ADD KEY `fk_facultad_universidad` (`id_universidad`);

--
-- Indices de la tabla `formato`
--
ALTER TABLE `formato`
  ADD PRIMARY KEY (`id_formato`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD UNIQUE KEY `pago_unico` (`dni_usuario`,`id_publicacion`),
  ADD KEY `id_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id_publicacion`),
  ADD KEY `fk_publicacion_usuario` (`dni_usuario`),
  ADD KEY `fk_publicacion_materia` (`id_materia`),
  ADD KEY `fk_publicacion_archivo` (`id_archivo`),
  ADD KEY `fk_publicacion_tipo_recurso` (`id_tipo_recurso`);

--
-- Indices de la tabla `tipo_recurso`
--
ALTER TABLE `tipo_recurso`
  ADD PRIMARY KEY (`id_tipo_recurso`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `universidad`
--
ALTER TABLE `universidad`
  ADD PRIMARY KEY (`id_universidad`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`dni_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuario_carrera` (`id_carrera`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivo`
--
ALTER TABLE `archivo`
  MODIFY `id_archivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `carrera_materia`
--
ALTER TABLE `carrera_materia`
  MODIFY `id_carrera_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `facultad`
--
ALTER TABLE `facultad`
  MODIFY `id_facultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `formato`
--
ALTER TABLE `formato`
  MODIFY `id_formato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `materia`
--
ALTER TABLE `materia`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id_publicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `tipo_recurso`
--
ALTER TABLE `tipo_recurso`
  MODIFY `id_tipo_recurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `universidad`
--
ALTER TABLE `universidad`
  MODIFY `id_universidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `archivo`
--
ALTER TABLE `archivo`
  ADD CONSTRAINT `fk_archivo_formato` FOREIGN KEY (`id_formato`) REFERENCES `formato` (`id_formato`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD CONSTRAINT `fk_carrera_facultad` FOREIGN KEY (`id_facultad`) REFERENCES `facultad` (`id_facultad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `carrera_materia`
--
ALTER TABLE `carrera_materia`
  ADD CONSTRAINT `fk_cm_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cm_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD CONSTRAINT `fk_facultad_universidad` FOREIGN KEY (`id_universidad`) REFERENCES `universidad` (`id_universidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`dni_usuario`) REFERENCES `usuario` (`dni_usuario`),
  ADD CONSTRAINT `pago_ibfk_2` FOREIGN KEY (`id_publicacion`) REFERENCES `publicacion` (`id_publicacion`);

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `fk_publicacion_archivo` FOREIGN KEY (`id_archivo`) REFERENCES `archivo` (`id_archivo`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publicacion_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publicacion_tipo_recurso` FOREIGN KEY (`id_tipo_recurso`) REFERENCES `tipo_recurso` (`id_tipo_recurso`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publicacion_usuario` FOREIGN KEY (`dni_usuario`) REFERENCES `usuario` (`dni_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
