<?php

# Locale settings
#setlocale(LC_ALL, 'es_MX');
date_default_timezone_set('America/Mexico_City');

# Set the error reporting level
if($_SERVER['SERVER_NAME']=="localico") {
    define("ENVIRONMENT", "DEVELOPMENT");
    define("SITE_URL", "http://localico/");
    define("PATH_ROOT", "C:/Users/ramir/PRE/Sites/iconoclast/");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    #error_reporting(0);
    #ini_set('display_errors', '0');
} else {
    define("ENVIRONMENT", "PRODUCTION");
    define("SITE_URL", "https://iconoclast.serviciosabp.com/");
    define("PATH_ROOT", "/home/servicio/public_html/iconoclast/");
    error_reporting(0);
    ini_set('display_errors', '0');
}

# Site settings
define("SITE_FOOTER_COPY", "");

# Database login details
define("HOST", "localhost");
define("DATABASE", "servicio_ico");
define("USER", "servicio_ico");
define("PASSWORD", 'HxJx8sgy25s9nSF8s');

# Secure settings
define("SEC_SESSION_ID", "sess_primo");
define("SEC_SALT", "PRIMO$301020");
define("SECURE_HTTP", "0");
define("HTTP_ONLY", "1");

# Path settings
define("PATH_CLASSES", PATH_ROOT . "includes/classes/");
define("PATH_PROJECTS", PATH_ROOT . "files/projects/");
define("PATH_VENDORS", PATH_ROOT . "files/vendors/");
define("PATH_COMPANIES", PATH_ROOT . "files/companies/");
define("PATH_SIGNATURES", PATH_ROOT . "files/signatures/");
define("PATH_DBUPDATE", PATH_ROOT . "files/db/");
define("PATH_MAILS", PATH_ROOT . "mails/");

# Mail settings
define("MAIL_FROM", "iconoclast@serviciosabp.com");
define("MAIL_FROM_NAME", "Plataforma ABP");
define("MAIL_HOST", "mail.serviciosabp.com");
define("MAIL_PORT", "465");
define("MAIL_USER", "iconoclast@serviciosabp.com");
define("MAIL_PSWD", "o7MEppRv2HoNq5c2");

# Other settings
define("PAYMENT_STATUS_PENDING", "1");
define("PAYMENT_STATUS_AUTHORIZED", "2");
define("PAYMENT_STATUS_PAYED", "3");
define("PAYMENT_STATUS_CANCELLED", "4");
define("CONTRACT_STATUS_PENDING", "1");
define("CONTRACT_STATUS_SIGNED", "2");
define("CONTRACT_STATUS_APROVED", "3");
define("CONTRACT_PROVEEDORES_PM", "1");
define("CONTRACT_SERVICIOS_PF", "2");
define("CARTA_NDA", "3");
define("ROLE_WEBMASTER", "1");
define("ROLE_VENDOR", "9");

# updates values
define("CONTRACTS_NEW_ID", "5200");

?>