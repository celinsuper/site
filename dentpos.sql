-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 27-10-2026 a las 17:33:05
-- Versión del servidor: 5.5.8
-- Versión de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `dentpos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE IF NOT EXISTS `citas` (
  `record_key` varchar(191) NOT NULL,
  `position_no` int(11) NOT NULL DEFAULT '0',
  `data_json` longtext NOT NULL,
  PRIMARY KEY (`record_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctores`
--

CREATE TABLE IF NOT EXISTS `doctores` (
  `record_key` varchar(191) NOT NULL,
  `position_no` int(11) NOT NULL DEFAULT '0',
  `data_json` longtext NOT NULL,
  PRIMARY KEY (`record_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE IF NOT EXISTS `insumos` (
  `record_key` varchar(191) NOT NULL,
  `position_no` int(11) NOT NULL DEFAULT '0',
  `data_json` longtext NOT NULL,
  PRIMARY KEY (`record_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_clinicas`
--

CREATE TABLE IF NOT EXISTS `notas_clinicas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` varchar(191) NOT NULL,
  `nota` text NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_paciente_id` (`paciente_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `odontogramas`
--

CREATE TABLE IF NOT EXISTS `odontogramas` (
  `record_key` varchar(191) NOT NULL,
  `position_no` int(11) NOT NULL DEFAULT '0',
  `data_json` longtext NOT NULL,
  PRIMARY KEY (`record_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE IF NOT EXISTS `pacientes` (
  `record_key` varchar(191) NOT NULL,
  `position_no` int(11) NOT NULL DEFAULT '0',
  `data_json` longtext NOT NULL,
  PRIMARY KEY (`record_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE IF NOT EXISTS `pagos` (
  `record_key` varchar(191) NOT NULL,
  `position_no` int(11) NOT NULL DEFAULT '0',
  `data_json` longtext NOT NULL,
  PRIMARY KEY (`record_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
