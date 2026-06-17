# 2026-06-16 - bank for vendors

UPDATE ico_proveedores SET banco = 'AZTECA' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%azteca%';
UPDATE ico_proveedores SET banco = 'BAJIO' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%bajio%';
UPDATE ico_proveedores SET banco = 'BANAMEX' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%banamex%';
UPDATE ico_proveedores SET banco = 'BANAMEX' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%Banmex%';
UPDATE ico_proveedores SET banco = 'BANAMEX' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%banco nacional%';
UPDATE ico_proveedores SET banco = 'BANCOPPEL' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%coppel%';
UPDATE ico_proveedores SET banco = 'BANCOPPEL' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%copeel%';
UPDATE ico_proveedores SET banco = 'BANORTE' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%banorte%';
UPDATE ico_proveedores SET banco = 'BANORTE' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%banco mercantil%';
UPDATE ico_proveedores SET banco = 'BANKAOOL' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%aool%';
UPDATE ico_proveedores SET banco = 'BANREGIO' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%regio%';
UPDATE ico_proveedores SET banco = 'BBVA MEXICO' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%bbva%';
UPDATE ico_proveedores SET banco = 'BBVA MEXICO' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%bancomer%';
UPDATE ico_proveedores SET banco = 'BMONEX' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%monex%';
UPDATE ico_proveedores SET banco = 'FONDEADORA' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%fondea%';
UPDATE ico_proveedores SET banco = 'HSBC' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%hsbc%';
UPDATE ico_proveedores SET banco = 'INBURSA' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%inbursa%';
UPDATE ico_proveedores SET banco = 'KAPITAL' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%kapital%';
UPDATE ico_proveedores SET banco = 'Mercado Pago' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%mercado%';
UPDATE ico_proveedores SET banco = 'MIFEL' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%mifel%';
UPDATE ico_proveedores SET banco = 'NU MEXICO' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%nu%';
UPDATE ico_proveedores SET banco = 'SANTANDER' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%santander%';
UPDATE ico_proveedores SET banco = 'SCOTIABANK' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%scotia%';
UPDATE ico_proveedores SET banco = 'STP' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%stp%';
UPDATE ico_proveedores SET banco = 'VE POR MAS' WHERE extranjero = 0 AND banco <> '' AND deleted = 0 AND banco LIKE '%ve por%';

UPDATE `ico_proveedores` SET `banco` = '' WHERE `ico_proveedores`.`proveedorId` = 2432;
UPDATE `ico_proveedores` SET `banco` = '' WHERE `ico_proveedores`.`proveedorId` = 2772;

UPDATE `ico_proveedores` SET `deleted` = '1' WHERE `ico_proveedores`.`proveedorId` = 2172;

# 2026-06-12 - notify vendors of payment complement missing

ALTER TABLE `ico_gastos` ADD `comprobanteNotify` DATE NULL DEFAULT NULL AFTER `comprobante`;

# 2026-05-08 - Initial setup

DELETE FROM ico_companies WHERE companyId = 1;
DELETE FROM ico_companies WHERE companyId = 3;

UPDATE `ico_usuarios` SET `companyId` = '2' WHERE `ico_usuarios`.`usuarioId` = 1;

DELETE FROM ico_usuarios WHERE companyId = 1;
DELETE FROM ico_usuarios WHERE companyId = 3;
DELETE FROM ico_usuarios WHERE `ico_usuarios`.`usuarioId` = 24;
DELETE FROM ico_usuarios WHERE deleted = 1;

