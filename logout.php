<?php
    require_once ('functions.php');

	function changeDb($db = 1) {
		$array = [
			1 => 'dms', 
			2 => 'dms_cap'
		];

		return new Database('localhost', 'root', 'tsu', $array[$db]);
	}

    $db = changeDb(isset($_COOKIE['unitid']) ? $_COOKIE['unitid'] : 1);

    $token = isset ($_GET['token']) ? $_GET['token'] : null;
    if (Session::destroy($db, $token)) {
        header('Location: ' . BASEURL . 'login.php');
        exit;
    } else {
        http_response_code(500);
        exit("Somethint went wrong...");
    }