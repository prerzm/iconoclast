-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 21, 2021 at 06:57 PM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `servicio_primo`
--

-- --------------------------------------------------------

--
-- Table structure for table `primo_modulos`
--

DROP TABLE IF EXISTS `primo_modulos`;
CREATE TABLE IF NOT EXISTS `primo_modulos` (
  `moduloId` int(11) NOT NULL AUTO_INCREMENT,
  `moduloKey` varchar(25) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `menuParentKey` varchar(25) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `menuParentName` varchar(25) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `menuFile` varchar(25) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `modulo` varchar(25) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `moduloFiles` varchar(500) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `orden` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`moduloId`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `primo_modulos`
--

INSERT INTO `primo_modulos` (`moduloId`, `moduloKey`, `menuParentKey`, `menuParentName`, `menuFile`, `modulo`, `moduloFiles`, `orden`, `deleted`) VALUES
(1, 'USERS', 'sistema', 'Sistema', 'users.php', 'Usuarios', 'users.php|users.edit.php', 95, 0),
(2, 'ROLES', 'sistema', 'Sistema', 'roles.php', 'Roles', 'roles.php|roles.edit.php', 94, 0),
(3, 'PERMS', 'sistema', 'Sistema', 'perms.php', 'Permisos', 'perms.php|perms.edit.php', 93, 0),
(4, 'MODULES', 'sistema', 'Sistema', 'modules.php', 'Módulos', 'modules.php|modules.edit.php', 91, 0),
(5, 'MASTER', 'proyectos', 'Proyectos', 'master.php', 'Presupuesto Maestro', 'master.php|master.edit.php', 24, 0),
(6, 'PROJECTS', 'proyectos', 'Proyectos', 'projects.php', 'Proyectos', 'projects.php|projects.edit.php', 22, 0),
(9, 'CUSTOMERS', 'proyectos', 'Proyectos', 'customers.php', 'Cuentas', 'customers.php|customers.edit.php', 20, 0),
(10, 'VENDORS', 'admin', 'Administración', 'vendors.php', 'Proveedores', 'vendors.php|vendors.view.php|vendors.edit.php', 13, 0),
(11, 'POS', 'admin', 'Administración', 'pos.php', 'Cuentas por Pagar', 'pos.php|pos.add.php|pos.edit.php|pos.view.php|pos.pay.php', 11, 0),
(14, 'REPORTS', 'reportes', 'Reportes', 'reports.php', 'Reportes', 'reports.php|reports.excel.php', 30, 0),
(15, 'BUDGETS', 'proyectos', 'Proyectos', 'budgets.php', 'Presupuestos', 'budgets.php|budgets.detail.php', 23, 0),
(16, 'DIRECTORS', 'proyectos', 'Proyectos', 'directors.php', 'Directores', 'directors.php|directors.edit.php', 21, 0),
(17, 'SYSTEM', 'sistema', 'Sistema', 'system.php', 'Mantenimiento', 'system.php', 97, 0),
(18, 'VENDOR_POS', 'proveedores', 'Proveedor', 'vendors.pos.php', 'Pagos', 'vendors.pos.php|vendors.pos.edit.php', 2, 0),
(19, 'VENDOR_INFO', 'proveedores', 'Proveedor', 'vendors.info.php', 'Información', 'vendors.info.php', 1, 0),
(21, 'CURRENCIES', 'sistema', 'Sistema', 'currencies.php', 'Monedas', 'currencies.php', 90, 0),
(22, 'WAGES', 'proyectos', 'Proyectos', 'wages.php', 'Nóminas', 'wages.php', 25, 0),
(23, 'SETTINGS', 'sistema', 'Sistema', 'settings.php', 'Configuración', 'settings.php', 92, 0),
(24, 'CONTRACTS', 'sistema', 'Sistema', 'contracts.php', 'Contratos', 'contracts.php|contracts.edit.php|contracts.add.php', 96, 0),
(25, 'VENDOR_CONTRACTS', 'proveedores', 'Proveedor', 'vendors.contracts.php', 'Contratos', 'vendors.contracts.php|vendors.contracts.form.php|vendors.contracts.sign.php', 3, 0),
(26, 'ADMIN_CONTRACTS', 'admin', 'Administración', 'contracts.admin.php', 'Contratos Proveedores', 'contracts.admin.php', 15, 0);

-- --------------------------------------------------------

--
-- Table structure for table `primo_modulos_permisos`
--

DROP TABLE IF EXISTS `primo_modulos_permisos`;
CREATE TABLE IF NOT EXISTS `primo_modulos_permisos` (
  `permisoId` int(11) NOT NULL AUTO_INCREMENT,
  `moduloId` int(11) NOT NULL DEFAULT '0',
  `permisoKey` varchar(50) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `permiso` varchar(50) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`permisoId`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `primo_modulos_permisos`
--

INSERT INTO `primo_modulos_permisos` (`permisoId`, `moduloId`, `permisoKey`, `permiso`) VALUES
(1, 1, 'READ', 'Acceso a Usuarios'),
(2, 1, 'EDIT', 'Editar Usuarios'),
(3, 1, 'DELETE', 'Eliminar Usuarios'),
(16, 4, 'READ', 'Acceso a Módulos'),
(6, 2, 'READ', 'Acceso a Roles'),
(7, 2, 'EDIT', 'Editar Roles'),
(8, 2, 'DELETE', 'Eliminar Roles'),
(9, 3, 'READ', 'Acceso a Permisos'),
(10, 3, 'EDIT', 'Editar Permisos'),
(11, 3, 'DELETE', 'Eliminar Permisos'),
(13, 1, 'ADD', 'Agregar Usuarios'),
(14, 2, 'ADD', 'Agregar Roles'),
(15, 3, 'ADD', 'Agregar Permisos'),
(17, 4, 'EDIT', 'Editar Módulos'),
(18, 4, 'DELETE', 'Eliminar Módulos'),
(19, 4, 'ADD', 'Agregar Módulos'),
(20, 5, 'READ', 'Acceso a Presupuesto'),
(21, 5, 'ADD', 'Agregar Concepto'),
(22, 5, 'EDIT', 'Editar Concepto'),
(23, 5, 'DELETE', 'Eliminar Concepto'),
(24, 6, 'READ', 'Acceso a Proyectos'),
(25, 6, 'EDIT', 'Editar Proyectos'),
(26, 6, 'DELETE', 'Eliminar Proyectos'),
(27, 6, 'ADD', 'Agregar Proyectos'),
(28, 9, 'ADD', 'Agregar Cuentas'),
(29, 9, 'EDIT', 'Editar Cuentas'),
(30, 9, 'DELETE', 'Eliminar Cuentas'),
(64, 9, 'READ', 'Acceso a Cuentas'),
(32, 10, 'READ', 'Acceso a Proveedores'),
(33, 10, 'ADD', 'Agregar Proveedores'),
(34, 10, 'EDIT', 'Editar Proveedores'),
(35, 10, 'DELETE', 'Eliminar Proveedores'),
(36, 11, 'READ', 'Acceso a Cuentas por Pagar'),
(37, 11, 'ADD', 'Agregar Cuentas por Pagar'),
(38, 11, 'EDIT', 'Editar Cuentas por Pagar'),
(39, 11, 'DELETE', 'Eliminar Cuentas por Pagar'),
(40, 11, 'AUTHORIZE', 'Autorizar Cuentas por Pagar'),
(41, 11, 'PAY', 'Pagar Cuentas por Pagar'),
(45, 14, 'READ', 'Acceso a Reportes'),
(54, 15, 'READ', 'Acceso a Presupuestos'),
(74, 15, 'EDIT', 'Editar Prespuestos'),
(76, 22, 'READ', 'Acceso a Nóminas'),
(57, 15, 'ADD', 'Agregar Presupuestos'),
(58, 16, 'ADD', 'Agregar Directores'),
(59, 16, 'EDIT', 'Editar Directores'),
(60, 16, 'READ', 'Acceso a Directores'),
(61, 16, 'DELETE', 'Eliminar Directores'),
(62, 17, 'READ', 'Acceso a Mantenimiento'),
(63, 17, 'EDIT', 'Editar Mantenimiento'),
(65, 18, 'READ', 'Acceso'),
(66, 18, 'EDIT', 'Editar'),
(67, 19, 'READ', 'Acceso a Información'),
(68, 19, 'EDIT', 'Editar Información'),
(71, 21, 'READ', 'Acceso a Monedas'),
(72, 21, 'EDIT', 'Editar Monedas'),
(75, 15, 'DELETE', 'Eliminar Presupuestos'),
(77, 22, 'ADD', 'Agregar Archivo de Nómina'),
(78, 23, 'READ', 'Acceso a Configuración'),
(79, 23, 'EDIT', 'Editar Configuración'),
(80, 23, 'DELETE', 'Eliminar Configuración'),
(81, 23, 'ADD', 'Agregar Configuración'),
(82, 23, 'FULL', 'Acceso Completo'),
(83, 24, 'READ', 'Acceso a Contratos'),
(84, 24, 'EDIT', 'Editar Contratos'),
(85, 25, 'READ', 'Acceso a Contratos'),
(86, 25, 'EDIT', 'Editar Contratos'),
(87, 26, 'READ', 'Acceso a Contratos'),
(89, 26, 'DELETE', 'Eliminar Contratos');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
