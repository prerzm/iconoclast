# 2025-01-13 - new contratos

-- 2024-12-13 - Added one more email test mode

INSERT INTO `primo_configuracion` (`configId`, `configKey`, `configCat`, `configName`, `configValue`, `configPublic`, `configType`, `configOptions`) VALUES
(27, 'VENDOR_EMAIL_MODE', 'Correos', 'Envío de correos: Envío de correos del sistema', '0', 0, 'Combo', '[{\"text\":\"Desactivado - Sin mostrar en pantalla\",\"value\":\"0\"},{\"text\":\"Desactivado - Mostrar en pantalla\",\"value\":\"1\"},{\"text\":\"Prueba - enviar únicamente al correo de la empresa\",\"value\":\"2\"},{\"text\":\"Activado - enviar a destinatarios\",\"value\":\"3\"}]');

-- 2024-10-17 - Nuevos Contratos

    -- Add file contracts.admin.detail.php to module ADMIN_CONTRACTS
    -- Add perm EDIT to Contratos Proveedores
    -- Add perm to Role Webmaster

    -- Add files vendors.info.invoice.php, vendors.info.bank.php & vendors.info.docs.php to module VENDOR_INFO

ALTER TABLE `primo_proveedores` ADD `repseReq` TINYINT NOT NULL DEFAULT '0' AFTER `razonSocial`;

ALTER TABLE `primo_proveedores` ADD `repseNumero` VARCHAR(30) NOT NULL DEFAULT '' AFTER `repseReq`;

ALTER TABLE `primo_proveedores` ADD `repseAviso` VARCHAR(30) NOT NULL DEFAULT '' AFTER `repseNumero`;

ALTER TABLE `primo_proveedores` CHANGE `editar` `editar` TINYINT(1) NOT NULL DEFAULT '1';

ALTER TABLE `primo_contratos_proveedores` ADD `parentId` INT NOT NULL DEFAULT '0' AFTER `id`;

ALTER TABLE `primo_contratos_proveedores` ADD `contratoId` INT NOT NULL DEFAULT '0' AFTER `proyectoId`;

ALTER TABLE `primo_contratos_proveedores` ADD `fechaCreado` DATE NULL DEFAULT '2024-01-01' AFTER `contratoId`;

ALTER TABLE `primo_contratos_proveedores` ADD `fieldsValues` TEXT NOT NULL AFTER `carta`;

UPDATE `primo_contratos_proveedores` SET `fechaCreado` = (`firmaFecha` - INTERVAL 1 WEEK) WHERE `fechaCreado` = '2024-01-01' AND `firmaFecha` IS NOT NULL;

ALTER TABLE `primo_contratos_proveedores` AUTO_INCREMENT = 5200;

#ALTER TABLE `primo_proyectos` ADD `fechaInicio` DATE NULL DEFAULT NULL AFTER `diaFilmacion`, ADD `fechaFin` DATE NULL DEFAULT NULL AFTER `fechaInicio`;

UPDATE `primo_proyectos` SET `diaFilmacion` = NULL WHERE `diaFilmacion` = '0000-00-00';

UPDATE `primo_proyectos` SET `fechaInicio` = `diaFilmacion`, `fechaFin` = `diaFilmacion`;

ALTER TABLE `primo_proyectos` ADD `lugar` VARCHAR(100) NOT NULL DEFAULT '' AFTER `fechaFin`;

UPDATE `primo_proyectos` SET `lugar` = 'CDMX';

# 2024-09-20 - new contract signing requirements

INSERT INTO `primo_configuracion` (`configId`, `configKey`, `configCat`, `configName`, `configValue`, `configPublic`, `configType`, `configOptions`) VALUES (NULL, 'VENDOR_REQ_ACTA', 'Proveedores', 'Requerir Acta Constitutiva a Proveedores', '0', '1', 'Radio', '[{\"text\":\"Si\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]');

# 2024-09-10 - upgrade to new upload system

CREATE TABLE IF NOT EXISTS `primo_files` (
  `fileId` int(11) NOT NULL AUTO_INCREMENT,
  `recordId` int(11) NOT NULL DEFAULT '0',
  `module` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `codeName` varchar(50) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `dateAdded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `original` varchar(200) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `path` varchar(200) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `saved` varchar(50) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


# 2024-09-04 - Add relationship between wages and pos

ALTER TABLE `primo_nominas` ADD `pos` VARCHAR(500) NOT NULL DEFAULT '' AFTER `monto`;

# 2024-06-24 - agregar campo de proveedores para repse

ALTER TABLE `primo_proveedores` ADD `repse` VARCHAR(200) NOT NULL DEFAULT '' AFTER `residencia_fecha`, ADD `repse_fecha` DATE NULL DEFAULT NULL AFTER `repse`;

UPDATE primo_proveedores SET repse_fecha = '2020-01-01';

INSERT INTO `primo_configuracion` (`configId`, `configKey`, `configCat`, `configName`, `configValue`, `configPublic`, `configType`, `configOptions`) VALUES (NULL, 'VENDOR_REQ_REPSE', 'Proveedores', 'Requerir REPSE o Carta a Proveedores', '1', '1', 'Radio', '[{\"text\":\"Si\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]');

# 2024-05-14 - change tmp password for vendor

ALTER TABLE `primo_proveedores` ADD `tmp` VARCHAR(40) NOT NULL DEFAULT '' AFTER `editar`;


# 2024-05-10 - update año de proyectos

UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 1;
UPDATE primo_proyectos SET ano = '2020' WHERE proyectoId = 2;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 3;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 4;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 5;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 6;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 7;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 8;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 9;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 10;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 11;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 12;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 13;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 14;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 15;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 16;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 17;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 18;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 19;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 20;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 21;
UPDATE primo_proyectos SET ano = '2020' WHERE proyectoId = 22;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 23;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 24;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 25;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 26;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 27;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 28;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 29;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 30;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 31;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 32;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 33;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 34;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 35;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 36;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 37;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 38;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 39;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 40;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 41;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 42;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 43;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 44;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 45;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 46;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 47;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 48;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 49;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 50;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 51;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 52;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 53;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 54;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 55;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 56;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 57;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 58;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 59;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 60;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 61;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 62;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 63;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 64;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 65;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 66;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 68;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 69;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 70;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 72;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 73;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 74;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 75;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 76;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 77;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 80;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 81;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 82;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 83;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 84;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 85;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 86;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 87;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 88;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 89;
UPDATE primo_proyectos SET ano = '2021' WHERE proyectoId = 90;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 91;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 92;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 93;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 94;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 96;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 97;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 98;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 99;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 100;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 101;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 102;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 103;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 104;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 105;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 106;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 107;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 108;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 109;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 110;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 111;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 112;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 113;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 114;
UPDATE primo_proyectos SET ano = '2022' WHERE proyectoId = 115;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 117;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 118;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 119;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 120;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 121;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 122;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 123;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 125;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 126;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 127;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 128;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 129;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 130;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 131;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 132;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 133;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 134;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 135;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 136;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 137;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 138;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 139;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 140;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 141;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 142;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 143;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 144;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 145;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 146;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 147;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 148;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 149;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 150;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 151;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 152;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 153;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 154;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 155;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 156;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 157;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 158;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 160;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 161;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 162;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 163;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 164;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 165;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 166;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 167;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 168;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 169;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 170;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 171;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 172;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 173;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 174;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 175;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 176;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 177;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 178;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 179;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 180;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 181;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 182;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 183;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 184;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 185;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 186;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 187;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 188;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 189;
UPDATE primo_proyectos SET ano = '2023' WHERE proyectoId = 190;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 191;
UPDATE primo_proyectos SET ano = '2024' WHERE proyectoId = 192;


# 2024-05-03 - agregar encuesta al subir factura

DROP TABLE IF EXISTS `primo_encuestas_respuestas`;
CREATE TABLE IF NOT EXISTS `primo_encuestas_respuestas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedorId` int(11) NOT NULL DEFAULT '0',
  `proyectoId` int(11) NOT NULL DEFAULT '0',
  `res1` tinyint(1) NOT NULL DEFAULT '0',
  `res2` tinyint(1) NOT NULL DEFAULT '0',
  `res3` tinyint(1) NOT NULL DEFAULT '0',
  `res4` varchar(500) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


# 2024-05-02 - agregar vigencia a constancia, opinion y comprobante de domicilio

ALTER TABLE `primo_proveedores` ADD `constancia_fecha` DATE NULL DEFAULT NULL AFTER `constancia`;
ALTER TABLE `primo_proveedores` ADD `opinionCumplimiento_fecha` DATE NULL DEFAULT NULL AFTER `opinionCumplimiento`;
ALTER TABLE `primo_proveedores` ADD `residencia_fecha` DATE NULL DEFAULT NULL AFTER `residencia`;

UPDATE primo_proveedores SET constancia_fecha = '2024-01-01', opinionCumplimiento_fecha = '2024-01-01', residencia_fecha = '2024-01-01'

UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-09' WHERE proveedorId = 14;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-12' WHERE proveedorId = 23;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-17' WHERE proveedorId = 37;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-16' WHERE proveedorId = 46;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-25' WHERE proveedorId = 47;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-15' WHERE proveedorId = 98;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-07' WHERE proveedorId = 132;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-27' WHERE proveedorId = 134;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-13' WHERE proveedorId = 164;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 186;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-17' WHERE proveedorId = 220;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-17' WHERE proveedorId = 234;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-26' WHERE proveedorId = 344;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-09' WHERE proveedorId = 346;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-14' WHERE proveedorId = 353;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-11' WHERE proveedorId = 396;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-11' WHERE proveedorId = 397;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-19' WHERE proveedorId = 398;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-12' WHERE proveedorId = 419;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 428;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-15' WHERE proveedorId = 429;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-12' WHERE proveedorId = 435;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 467;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-11' WHERE proveedorId = 550;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-23' WHERE proveedorId = 572;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-05' WHERE proveedorId = 600;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-23' WHERE proveedorId = 615;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 650;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-12' WHERE proveedorId = 685;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-29' WHERE proveedorId = 722;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 726;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-21' WHERE proveedorId = 807;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-14' WHERE proveedorId = 808;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-04' WHERE proveedorId = 855;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 860;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-27' WHERE proveedorId = 868;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-25' WHERE proveedorId = 911;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-11' WHERE proveedorId = 921;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-12' WHERE proveedorId = 923;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-27' WHERE proveedorId = 985;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-05' WHERE proveedorId = 1014;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-01' WHERE proveedorId = 1050;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-23' WHERE proveedorId = 1054;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-05' WHERE proveedorId = 1055;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-24' WHERE proveedorId = 1067;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-23' WHERE proveedorId = 1072;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-15' WHERE proveedorId = 1075;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-08' WHERE proveedorId = 1104;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-26' WHERE proveedorId = 1160;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-21' WHERE proveedorId = 1182;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-07' WHERE proveedorId = 1246;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-12' WHERE proveedorId = 1260;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-17' WHERE proveedorId = 1272;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-20' WHERE proveedorId = 1279;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-31' WHERE proveedorId = 1363;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-26' WHERE proveedorId = 1389;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-12' WHERE proveedorId = 1409;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-25' WHERE proveedorId = 1459;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-06' WHERE proveedorId = 1462;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-22' WHERE proveedorId = 1486;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-13' WHERE proveedorId = 1504;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-01' WHERE proveedorId = 1559;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-12' WHERE proveedorId = 1576;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-12' WHERE proveedorId = 1586;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-07' WHERE proveedorId = 1598;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-26' WHERE proveedorId = 1600;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-14' WHERE proveedorId = 1614;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-30' WHERE proveedorId = 1625;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-01-29' WHERE proveedorId = 1626;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-09' WHERE proveedorId = 1630;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-10' WHERE proveedorId = 1632;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-11' WHERE proveedorId = 1633;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-10' WHERE proveedorId = 1634;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-08' WHERE proveedorId = 1635;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-19' WHERE proveedorId = 1636;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-15' WHERE proveedorId = 1637;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-15' WHERE proveedorId = 1638;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-09' WHERE proveedorId = 1639;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-13' WHERE proveedorId = 1640;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-20' WHERE proveedorId = 1641;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-09' WHERE proveedorId = 1642;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-12' WHERE proveedorId = 1643;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-21' WHERE proveedorId = 1644;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-13' WHERE proveedorId = 1646;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-14' WHERE proveedorId = 1647;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-14' WHERE proveedorId = 1648;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-22' WHERE proveedorId = 1649;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-18' WHERE proveedorId = 1650;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-02-16' WHERE proveedorId = 1651;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-11' WHERE proveedorId = 1660;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-03' WHERE proveedorId = 1661;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-24' WHERE proveedorId = 1662;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-11' WHERE proveedorId = 1663;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-08' WHERE proveedorId = 1664;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-19' WHERE proveedorId = 1665;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-15' WHERE proveedorId = 1666;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-14' WHERE proveedorId = 1667;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-13' WHERE proveedorId = 1668;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-13' WHERE proveedorId = 1670;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 1671;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-13' WHERE proveedorId = 1672;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-19' WHERE proveedorId = 1673;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 1674;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-12' WHERE proveedorId = 1675;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-21' WHERE proveedorId = 1679;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 1687;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 1688;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-05' WHERE proveedorId = 1689;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-21' WHERE proveedorId = 1690;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-22' WHERE proveedorId = 1691;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 1692;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 1693;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-20' WHERE proveedorId = 1694;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-03-22' WHERE proveedorId = 1695;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-17' WHERE proveedorId = 1698;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-15' WHERE proveedorId = 1699;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-15' WHERE proveedorId = 1700;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-19' WHERE proveedorId = 1701;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 1702;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-15' WHERE proveedorId = 1703;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-12' WHERE proveedorId = 1705;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 1706;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-20' WHERE proveedorId = 1708;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-12' WHERE proveedorId = 1709;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-22' WHERE proveedorId = 1713;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-15' WHERE proveedorId = 1715;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 1716;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-17' WHERE proveedorId = 1717;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-17' WHERE proveedorId = 1718;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-19' WHERE proveedorId = 1719;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 1721;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-21' WHERE proveedorId = 1722;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-18' WHERE proveedorId = 1724;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-16' WHERE proveedorId = 1725;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-19' WHERE proveedorId = 1726;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-16' WHERE proveedorId = 1727;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-23' WHERE proveedorId = 1728;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-19' WHERE proveedorId = 1729;
UPDATE primo_proveedores SET opinionCumplimiento_fecha = '2024-04-22' WHERE proveedorId = 1730;


UPDATE primo_proveedores SET constancia_fecha = '2024-02-09' WHERE proveedorId = 14;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-12' WHERE proveedorId = 23;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 37;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-16' WHERE proveedorId = 46;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-25' WHERE proveedorId = 47;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-15' WHERE proveedorId = 98;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-10' WHERE proveedorId = 106;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-07' WHERE proveedorId = 132;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-27' WHERE proveedorId = 134;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-13' WHERE proveedorId = 164;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 186;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 220;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 234;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-24' WHERE proveedorId = 280;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-26' WHERE proveedorId = 344;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-09' WHERE proveedorId = 346;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-14' WHERE proveedorId = 353;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-11' WHERE proveedorId = 396;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-11' WHERE proveedorId = 397;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-19' WHERE proveedorId = 398;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 400;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-12' WHERE proveedorId = 419;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 428;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-15' WHERE proveedorId = 429;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-12' WHERE proveedorId = 435;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 467;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-11' WHERE proveedorId = 550;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-23' WHERE proveedorId = 572;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-05' WHERE proveedorId = 600;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-23' WHERE proveedorId = 615;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 650;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-12' WHERE proveedorId = 685;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-29' WHERE proveedorId = 722;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 726;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-21' WHERE proveedorId = 807;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-14' WHERE proveedorId = 808;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-04' WHERE proveedorId = 855;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 860;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-27' WHERE proveedorId = 868;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-25' WHERE proveedorId = 911;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-11' WHERE proveedorId = 921;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-12' WHERE proveedorId = 923;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-27' WHERE proveedorId = 985;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-05' WHERE proveedorId = 1014;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-01' WHERE proveedorId = 1050;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-23' WHERE proveedorId = 1054;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-05' WHERE proveedorId = 1055;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-24' WHERE proveedorId = 1067;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-23' WHERE proveedorId = 1072;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-15' WHERE proveedorId = 1075;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-08' WHERE proveedorId = 1104;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-26' WHERE proveedorId = 1160;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-21' WHERE proveedorId = 1182;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-07' WHERE proveedorId = 1246;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-12' WHERE proveedorId = 1260;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 1272;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-20' WHERE proveedorId = 1279;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-09' WHERE proveedorId = 1363;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-26' WHERE proveedorId = 1389;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-12' WHERE proveedorId = 1409;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-25' WHERE proveedorId = 1459;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-06' WHERE proveedorId = 1462;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-22' WHERE proveedorId = 1486;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-13' WHERE proveedorId = 1504;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-01' WHERE proveedorId = 1559;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-12' WHERE proveedorId = 1576;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-12' WHERE proveedorId = 1586;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-07' WHERE proveedorId = 1598;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-26' WHERE proveedorId = 1600;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-14' WHERE proveedorId = 1614;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-30' WHERE proveedorId = 1625;
UPDATE primo_proveedores SET constancia_fecha = '2024-01-29' WHERE proveedorId = 1626;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-09' WHERE proveedorId = 1630;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-10' WHERE proveedorId = 1632;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-10' WHERE proveedorId = 1633;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-10' WHERE proveedorId = 1634;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-08' WHERE proveedorId = 1635;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-19' WHERE proveedorId = 1636;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-15' WHERE proveedorId = 1637;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-15' WHERE proveedorId = 1638;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-09' WHERE proveedorId = 1639;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-13' WHERE proveedorId = 1640;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-20' WHERE proveedorId = 1641;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-09' WHERE proveedorId = 1642;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-12' WHERE proveedorId = 1643;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-21' WHERE proveedorId = 1644;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-13' WHERE proveedorId = 1646;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-14' WHERE proveedorId = 1647;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-14' WHERE proveedorId = 1648;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-22' WHERE proveedorId = 1649;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-18' WHERE proveedorId = 1650;
UPDATE primo_proveedores SET constancia_fecha = '2024-02-16' WHERE proveedorId = 1651;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-09' WHERE proveedorId = 1660;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-03' WHERE proveedorId = 1661;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-24' WHERE proveedorId = 1662;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-11' WHERE proveedorId = 1663;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-08' WHERE proveedorId = 1664;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-19' WHERE proveedorId = 1665;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-15' WHERE proveedorId = 1666;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-10' WHERE proveedorId = 1667;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-13' WHERE proveedorId = 1668;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-13' WHERE proveedorId = 1670;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 1671;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-13' WHERE proveedorId = 1672;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-19' WHERE proveedorId = 1673;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 1674;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-12' WHERE proveedorId = 1675;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-21' WHERE proveedorId = 1679;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 1687;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 1688;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-05' WHERE proveedorId = 1689;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-21' WHERE proveedorId = 1690;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-22' WHERE proveedorId = 1691;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 1692;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 1693;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-20' WHERE proveedorId = 1694;
UPDATE primo_proveedores SET constancia_fecha = '2024-03-22' WHERE proveedorId = 1695;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 1698;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-15' WHERE proveedorId = 1699;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-15' WHERE proveedorId = 1700;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-19' WHERE proveedorId = 1701;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 1702;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-15' WHERE proveedorId = 1703;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-12' WHERE proveedorId = 1705;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 1706;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-20' WHERE proveedorId = 1708;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-12' WHERE proveedorId = 1709;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-22' WHERE proveedorId = 1713;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-15' WHERE proveedorId = 1715;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 1716;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 1717;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-17' WHERE proveedorId = 1718;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-19' WHERE proveedorId = 1719;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 1721;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-21' WHERE proveedorId = 1722;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-18' WHERE proveedorId = 1724;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-16' WHERE proveedorId = 1725;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-19' WHERE proveedorId = 1726;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-16' WHERE proveedorId = 1727;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-22' WHERE proveedorId = 1728;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-19' WHERE proveedorId = 1729;
UPDATE primo_proveedores SET constancia_fecha = '2024-04-22' WHERE proveedorId = 1730;


# 2024-04-29 - agregar año a proyectos

ALTER TABLE `primo_proyectos` ADD `ano` VARCHAR(4) NOT NULL DEFAULT '' AFTER `clave`;

ALTER TABLE `primo_gastos` CHANGE `transfer` `transfer` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '', CHANGE `transfer2` `transfer2` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '', CHANGE `transfer3` `transfer3` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT ''; 

# 2024-04-24 - default values

ALTER TABLE `primo_gastos` CHANGE `transfer` `transfer` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '', CHANGE `transfer2` `transfer2` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '', CHANGE `transfer3` `transfer3` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT ''; 

# 2023-11-28 - prontos pagos

ALTER TABLE `primo_gastos` ADD `prontoPago` TINYINT(1) NOT NULL DEFAULT '0' AFTER `fechaDePago`;

# 2021-08-26 - foreign vendors

ALTER TABLE `primo_proveedores`  ADD `extranjero` TINYINT(1) NOT NULL DEFAULT '0'  AFTER `razonSocial`;

# 2021-08-13 - tipo de cambio

ALTER TABLE `primo_gastos` CHANGE `tipoDeCambio` `tipoDeCambio` DECIMAL(10,6) NOT NULL DEFAULT '0.0000';

# unused field

ALTER TABLE `primo_gastos` DROP `facturaTipo`;

# accepting 3 transfer pdfs
ALTER TABLE `primo_gastos` ADD `transfer2` VARCHAR(150) NOT NULL DEFAULT ''  AFTER `transfer`,  ADD `transfer3` VARCHAR(150) NOT NULL DEFAULT ''  AFTER `transfer2`;

# 2023-04-03 - Documentos proveedores extranjeros

ALTER TABLE primo_proveedores  ADD residencia VARCHAR(200) NOT NULL DEFAULT ''  AFTER identificacion;

INSERT INTO primo_configuracion (`configId`, `configKey`, `configCat`, `configName`, `configValue`, `configPublic`, `configType`, `configOptions`) VALUES (NULL, 'VENDOR_REQ_RESIDENCY', 'Proveedores', 'Proveedores: Requerir Comprobante de Residencia Fiscal', '0', '1', 'Radio', '[{\"text\":\"Si\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]');

# 2023-10-13 - Campos proveedores

ALTER TABLE `primo_proveedores` ADD `swift` VARCHAR(30) NOT NULL DEFAULT '' AFTER `clabe`;
ALTER TABLE `primo_proveedores` ADD `aba` VARCHAR(30) NOT NULL DEFAULT '' AFTER `swift`;

