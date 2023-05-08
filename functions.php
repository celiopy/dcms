<?php
    require_once('config.php');
    require_once(ABSPATH . 'inc/class.Database.php');
    require_once(ABSPATH . 'inc/class.Session.php');
    require_once(ABSPATH . 'inc/class.User.php');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);