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

