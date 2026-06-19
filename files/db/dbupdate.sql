# 2026-06-18 - Fix for payment date calculation

ALTER TABLE `ico_gastos` ADD `pagoDias` TINYINT NOT NULL DEFAULT '35' AFTER `fechaFixed`;

