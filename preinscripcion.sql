-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-09-2025 a las 18:12:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `preinscripcion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `comentario` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de tabla para la tabla `recuperacion_password`
--

CREATE TABLE `recuperacion_password` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `anio_egreso` varchar(20) NOT NULL,
  `colegio_egresado` varchar(150) NOT NULL,
  `sexo` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `telefono_emergencia` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `cedula_numero` varchar(20) NOT NULL,
  `distrito` varchar(100) NOT NULL,
  `condicion_medica` varchar(150) DEFAULT NULL,
  `condicion_especifica` varchar(150) DEFAULT NULL,
  `como_se_entero` varchar(100) DEFAULT NULL,
  `nacionalidad` varchar(50) NOT NULL,
  `carrera` varchar(100) NOT NULL,
  `foto_anverso_cedula` longblob NOT NULL,
  `foto_reverso_cedula` longblob NOT NULL,
  `foto_anverso_certificado` longblob NOT NULL,
  `foto_reverso_certificado` longblob NOT NULL,
  `antecedente_policial` longblob NOT NULL,
  `cert_medic` longblob NOT NULL,
  `cert_nacim` longblob NOT NULL,
  `foto_carnet` longblob NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol_id` int(11) DEFAULT 2,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `recuperacion_password`
--
ALTER TABLE `recuperacion_password`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `recuperacion_password`
--
ALTER TABLE `recuperacion_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `recuperacion_password`
--
ALTER TABLE `recuperacion_password`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
