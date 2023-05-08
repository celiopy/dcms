<?php
/** caminho absoluto para a pasta do sistema **/
if ( !defined('ABSPATH') ) 
	define('ABSPATH', dirname(__FILE__) . '/');

/** caminho no server para o sistema **/
if ( !defined('BASEURL') ) 
	define('BASEURL', '/');

/** caminhos dos templates de header e footer **/
define('HEADER_TEMPLATE', ABSPATH . '_partials/header.html');
define('FOOTER_TEMPLATE', ABSPATH . '_partials/footer.html');

$title = "DCMS";

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR.utf8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);