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

    include (ABSPATH . '_partials/header.html');
?>
    <div class="bar | flex items-center gap-400">
        <h3>Configs.</h3>
    </div>
    <main class="main">
        Settings.
    </main>
<?php include (ABSPATH . '_partials/footer.html'); ?>