<?php

/** RZM PHP Framework **/

# include configuration file
include_once('inc.config.php');

# include connect & db tables file
include_once('inc.db.connect.php');
include_once("inc.db.tables.php");

# include files
include_once("lib.vars.php");
include_once("lib.database.php");
include_once("lib.sessions.php");
include_once("lib.login.php");
include_once("lib.currencies.php");
include_once("lib.misc.php");
include_once("lib.html.php");
include_once("lib.perms.php");
include_once("lib.files.php");

# include app's specific functions
include_once("lib.abp.php");

# include autoloads
include_once(PATH_ROOT."vendor/autoload.php");
include_once("autoload.php");

# start secure session
session_secure_start();

# verify login status
redirect_if_logged_off(login_check($mysqli), basename($_SERVER['PHP_SELF']));

# get user menu & permissions
$global_menu = role_get_menu(session_get_data("roleId"));
$global_active_menu = get_active_menu($_SERVER['PHP_SELF']);
$global_perms = perm_get_role_permissions($_SERVER['PHP_SELF']);

# get currencies
$global_currencies = get_currencies();

# get companies
$global_companies = get_user_companies();
$global_company = get_user_company();

# set params for redirect
$global_params = array();

# load settings from db
load_settings();


?>