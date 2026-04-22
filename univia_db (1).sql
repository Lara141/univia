-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-04-2026 a las 16:51:57
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivo`
--

CREATE TABLE `archivo` (
  `id_archivo` int(11) NOT NULL,
  `nombre_archivo` varchar(80) NOT NULL,
  `ruta` varchar(100) NOT NULL,
  `formato` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `archivo`
--

INSERT INTO `archivo` (`id_archivo`, `nombre_archivo`, `ruta`, `formato`) VALUES
(1, 'GUIA DE TP N° 0 (1).pdf', 'uploads/archivos/1776864312_c5e07c45f99e093b91de.pdf', 'application/pdf'),
(2, 'GUIA DE TP N° 0 (1).pdf', 'uploads/archivos/1776864599_b0bccf58dc636cab773d.pdf', 'application/pdf'),
(3, 'Arquitecturas (1).pdf', 'uploads/archivos/1776865102_7c73ceaba184689dedcb.pdf', 'application/pdf'),
(4, 'GUÍA DE EJERCICIOS PRÁCTICOS 2025 (ECO APLICADA).pdf', 'uploads/archivos/1776866415_096a7cdfcb85fc2421d7.pdf', 'application/pdf'),
(5, 'GUÍA DE EJERCICIOS PRÁCTICOS 2025 (ECO APLICADA).pdf', 'uploads/archivos/1776866891_515dd11a3d044833cfaa.pdf', 'application/pdf'),
(6, 'Economía Aplicada 2026.pdf', 'uploads/archivos/1776867299_d18aeef0d50d209b81be.pdf', 'application/pdf'),
(7, 'Anexo_A_-_Manual_de_Usuarios_del_sistema.pdf', 'uploads/archivos/1776868102_994eb874cd360219074e.pdf', 'application/pdf');

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
(1, 'Licenciatura en Sistemas de la Información', 1);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudad`
--

CREATE TABLE `ciudad` (
  `id_ciudad` int(11) NOT NULL,
  `nombre_ciudad` varchar(200) NOT NULL,
  `id_pais` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudad`
--

INSERT INTO `ciudad` (`id_ciudad`, `nombre_ciudad`, `id_pais`) VALUES
(1, 'Corrientes', 1);

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
(1, 'FaCENA', 1);

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
(1, 'Programación I'),
(2, 'Análisis Matemático II'),
(3, 'Álgebra y Geometría'),
(4, 'Ingeniería de software 2'),
(5, 'Economia aplicada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pais`
--

CREATE TABLE `pais` (
  `id_pais` int(11) NOT NULL,
  `nombre_pais` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pais`
--

INSERT INTO `pais` (`id_pais`, `nombre_pais`) VALUES
(1, 'Argentina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id_publicacion` int(11) NOT NULL,
  `titulo` varchar(80) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `tipo_recurso` varchar(13) DEFAULT NULL,
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

INSERT INTO `publicacion` (`id_publicacion`, `titulo`, `descripcion`, `tipo_recurso`, `tipo_acuerdo`, `precio`, `fecha_publicacion`, `estado`, `dni_usuario`, `id_materia`, `id_archivo`) VALUES
(2, 'trabajo de ingenieria 2', 'este es el trabajo practico 0 de la materia ingeniería de software 2', 'guia', 'pago', NULL, '2026-04-22', 1, 45876789, 1, 2),
(3, 'tp1', 'dfggd', 'apunte', 'pago', 677.99, '2026-04-22', 1, 45876789, 5, 3),
(5, 'economia', 'practico de economia ', 'resumen', 'gratis', NULL, '2026-04-22', 1, 45876789, 5, 5),
(6, 'sdfe', 'sfer', 'examen', 'pago', NULL, '2026-04-22', 0, 45876789, 5, 6),
(7, 'trabajo de ingenieria 2', 'este es el trabajo practico 0 de la materia ingeniería de software 2', 'guia', 'pago', 405.00, '2026-04-22', 0, 45876789, 4, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `universidad`
--

CREATE TABLE `universidad` (
  `id_universidad` int(11) NOT NULL,
  `nombre_universidad` varchar(200) NOT NULL,
  `id_ciudad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `universidad`
--

INSERT INTO `universidad` (`id_universidad`, `nombre_universidad`, `id_ciudad`) VALUES
(1, 'UNNE', 1);

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
(45678903, 'edf@gmail.com', 'de5fr6gt7.*rdf', '2026-04-22', 1, 'ds', 'de', 1),
(45876789, 'diamelaacosta2004@gmail.com', 'Pancha78*', '2026-04-22', 1, 'Lara', 'Acosta', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivo`
--
ALTER TABLE `archivo`
  ADD PRIMARY KEY (`id_archivo`);

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
-- Indices de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`id_ciudad`),
  ADD KEY `fk_ciudad_pais` (`id_pais`);

--
-- Indices de la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD PRIMARY KEY (`id_facultad`),
  ADD KEY `fk_facultad_universidad` (`id_universidad`);

--
-- Indices de la tabla `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indices de la tabla `pais`
--
ALTER TABLE `pais`
  ADD PRIMARY KEY (`id_pais`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id_publicacion`),
  ADD KEY `fk_publicacion_usuario` (`dni_usuario`),
  ADD KEY `fk_publicacion_materia` (`id_materia`),
  ADD KEY `fk_publicacion_archivo` (`id_archivo`);

--
-- Indices de la tabla `universidad`
--
ALTER TABLE `universidad`
  ADD PRIMARY KEY (`id_universidad`),
  ADD KEY `fk_universidad_ciudad` (`id_ciudad`);

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
  MODIFY `id_archivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `carrera_materia`
--
ALTER TABLE `carrera_materia`
  MODIFY `id_carrera_materia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  MODIFY `id_ciudad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `facultad`
--
ALTER TABLE `facultad`
  MODIFY `id_facultad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `materia`
--
ALTER TABLE `materia`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pais`
--
ALTER TABLE `pais`
  MODIFY `id_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id_publicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `universidad`
--
ALTER TABLE `universidad`
  MODIFY `id_universidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

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
-- Filtros para la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD CONSTRAINT `fk_ciudad_pais` FOREIGN KEY (`id_pais`) REFERENCES `pais` (`id_pais`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `facultad`
--
ALTER TABLE `facultad`
  ADD CONSTRAINT `fk_facultad_universidad` FOREIGN KEY (`id_universidad`) REFERENCES `universidad` (`id_universidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `fk_publicacion_archivo` FOREIGN KEY (`id_archivo`) REFERENCES `archivo` (`id_archivo`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publicacion_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_publicacion_usuario` FOREIGN KEY (`dni_usuario`) REFERENCES `usuario` (`dni_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `universidad`
--
ALTER TABLE `universidad`
  ADD CONSTRAINT `fk_universidad_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudad` (`id_ciudad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
