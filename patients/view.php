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

    $user_id = User::is_logged_in($db);

    if ($user_id === false) {
        header('Location: ' . BASEURL . 'login.php');
        exit;
    }

    $title = "Pacientes - " . $title;
    include (ABSPATH . '_partials/header.html');
?>

<?php
    $patient = new Patient($db, $_GET['id']);
    $patient = $patient->get();

    if(! $patient) {
        echo "Ué, esse cara nem existe.";
    } else {
        echo "<h1>{$patient['id']}</h1>";
        echo "<h3>{$patient['Name']}</h3>";
    }
?>