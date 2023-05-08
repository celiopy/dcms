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

    $resultsPerPage = $_GET['limit'] ?? 10;
    $page = $_GET['page'] ?? 1;
    $query = $_GET['query'] ?? null;
    $searchResults = Patient::search($db, $page, $resultsPerPage, $query);
    $totalResults = $searchResults['totalResults'];

    // Return the results as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'results' => $searchResults['results'], 
        'page' => $page, 
        'totalResults' => $totalResults
    ]);