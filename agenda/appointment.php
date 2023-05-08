<?php
    require_once('functions.php');
    require_once(ABSPATH . 'inc/class.Apt.php');

    function changeDb($db = 1) {
        $array = [
            1 => 'dms', 
            2 => 'dms_cap'
        ];

        return new Database('localhost', 'root', 'tsu', $array[$db]);
    }

    $db = changeDb(isset($_COOKIE['unitid']) ? $_COOKIE['unitid'] : 1);

    if (isset($_GET['action'])) {
        if ($_GET['action'] == "update") {
            $apt = new Apt($db, $_GET['id']);
            $event = $apt->update($_POST['event']);
        } elseif ($_GET['action'] == "add") {
            $apt = new Apt($db);
            $event = $apt->add($_POST['event']);
        }

        exit;
    }

    // Usage example
    $apt = new Apt($db);

    $event = false;
    if (isset($_GET['id'])) {
        $event = $apt->getAppointments(null, null, $_GET['id'], null);
    }

    require 'aptlayout.php';