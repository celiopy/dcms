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
    <main class="main">
        <div class="flex items-center gap-400">
            <h3>Dashboard</h3>
            <div style="margin-inline-start: auto;">
                <button class="big">Dentistas</button>
                <button class="big o">Caixa</button>
            </div>
        </div>
        <?php
            $units = [
                1 => 'Guilherme', 
                2 => 'Cap. Teixeira'
            ];
        ?>
        <h1><?= $units[$_COOKIE['unitid']]; ?></h1>
        <h1>Hello, <?php echo User::get($db, $user_id, 'Name'); ?></h1>
        <a href="agenda/">Agenda</a>
        <div>
            Abrir em nova aba <i class="las la-external-link-alt"></i>
        </div>
        <div>
        <?php
            $sessions = Session::get($db, $user_id);
            foreach ($sessions as $session) {
                echo "<div style=\"max-width: 100%; word-wrap: break-word;\" class=\"" . ($session['active'] ? '' : 'inactive') . "\">";
                echo $session['UserID'] . ' - TOKEN :: ' . $session['active'];
                if ($session['token'] === $_COOKIE['auth0']) {
                    echo ' &bullet;';
                }
                echo $session['ip'];
                echo '</div>';
            }
        ?>
    </main>
<?php include (ABSPATH . '_partials/footer.html'); ?>