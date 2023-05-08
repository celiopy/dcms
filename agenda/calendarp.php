<?php
	require_once('functions.php');
    require_once('calendar.php');

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	function changeDb($db = 1) {
		$array = [
			1 => 'dms', 
			2 => 'dms_cap'
		];
	
		return new Database('localhost', 'root', 'tsu', $array[$db]);
	}
	
	$db = changeDb(isset($_COOKIE['unitid']) ? $_COOKIE['unitid'] : 1);

	$year = isset ($_GET['year']) ? $_GET['year'] : date('Y');
	$month = isset ($_GET['month']) ? $_GET['month'] : date('m');
	$day = isset ($_GET['day']) ? $_GET['day'] : date('d');
    $pretty = $_GET['doctor'] ?? null;

    $calendar = new Calendar($year, $month, $day, $pretty);
?>
<div style="background: var(--clr-surface); padding-block: 0.5rem 0.25rem; padding-inline: 0.25rem;">
	<?= $calendar->display(); ?>
</div>
<ul class="doctors">
	<?php if ( $doctors = doctors($db) ) { ?>
		<?php foreach ( $doctors as $doctor ): ?>
			<li style="--accent: <?= $doctor['Color']; ?>;">
				<input name="doctor" type="radio" id="<?= $doctor['Pretty']; ?>" value="<?= $doctor['Pretty']; ?>"<?= ( $pretty == $doctor['Pretty'] ? ' data-prev="false" checked' : '' ); ?>>
				<label for="<?= $doctor['Pretty']; ?>">
					<?= $doctor['Name']; ?>
				</label>
			</li>
		<?php endforeach; ?>
	<?php } ?>
</ul>