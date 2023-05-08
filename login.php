<?php
	require_once('functions.php');

	$message = "";

	function changeDb($db = 1) {
		$array = [
			1 => 'dms', 
			2 => 'dms_cap'
		];

		return new Database('localhost', 'root', 'tsu', $array[$db]);
	}

	$unitid = $_COOKIE['unitid'] ?? 1;

	$db = changeDb($unitid);

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// If the user is not logged in, display the login form
		// PHP code to handle form submission
		if (isset($_POST['username']) && isset($_POST['password'])) {
			setcookie('unitid', $_POST['unit'], time() + (10 * 365 * 24 * 60 * 60), '/', '', false, true);
			$unitid = $_POST['unit'];
			$db = changeDb($unitid);
	
			if (!empty($_POST['username']) && !empty($_POST['password'])) {
				// Create a new user object
				$user = new User($db, $_POST['username'], $_POST['password']);
			
				try {
					// Authenticate the user
					$user_id = $user->authenticate();
				
					// Create a new session
					$session = new Session($db, $user_id);
					$token = $session->create();
				
					// Store the token in a cookie
					setcookie('auth0', $token, time() + (10 * 365 * 24 * 60 * 60), '/', '', false, true);
	
					// Redirect the user to the home page
					header('Location: ' . BASEURL . 'index.php');
					exit();
				} catch (Exception $e) {
					// Display error message
					$message = $e->getMessage();
				}
			} else {
				$message = "Preencha TODOS os campos";
			}
		}
	}
	
	$user_id = User::is_logged_in($db);

	if ($user_id !== false) {
		header('Location: ' . BASEURL . 'index.php');
		exit;
	}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Login is different</title>
</head>
<style>
	* {
		margin: 0;
		padding: 0;
		font: inherit;
	}

	*, *::after, *::before {
		box-sizing: border-box;
	}

	html, body {
		min-width: 480px;
		height: 100%;
		font-family: sans-serif;
	}

	.login {
		display: flex;
		flex-direction: wrap;

		height: 100vh;
	}

	@media (max-width: 736px) {
		.login {
			flex-direction: column;
		}
	}

	.bg {
		background-image: url('assets/img/doctor.jpg');
		background-size: cover;
		background-position: right 25%;
	
		min-width: 30vw;
		min-height: 30vh;
	}

	.login-content {
		display: flex;
		flex-direction: column;
		justify-content: center;

		padding-inline: 2rem;
		padding-block: 1rem;
		gap: 1rem;
	}

	input, select {
		padding-inline: 1rem;
		display: block;

		width: 100%;

		border-radius: .5rem;
		border: 1px solid #ccc;

		height: 3rem;
	}

	button {
		display: block;
		width: 100%;

		height: 3rem;

		border: 1px solid #ccc;
		border-radius: .5rem;

		cursor: pointer;
	}

	h1 {
		font-size: 2rem;
	}

	.msg {
		padding-inline: 1rem;
		padding-block: 0.25rem;

		color: red;

		border-left: 2px solid;
	}

	strong {
		font-weight: bold;
	}
</style>
<body>
	<form method="POST" class="login">
		<div class="bg"></div>
		<div class="login-content">
			<div class="field">
				<h2>ASHE<strong>LABS</strong></h2>
			</div>
			<div><br></div>
			<div class="field">
				<h1>Acesse.</h1>
			</div>
			<?php 
				echo $unitid;

				if ($message) {
					echo '<div class="field | msg">' . $message . '</div>';
				}
			?>
			<div class="field">
				<select name="unit" id="" required>
					<option value="1"<?= ($unitid == 1 ? ' selected' : ''); ?>>Guilherme</option>
					<option value="2"<?= ($unitid == 2 ? ' selected' : ''); ?>>Cap. Teixeira</option>
				</select>
			</div>
			<div class="field"><input type="text" name="username" value="" placeholder="Usuário" required></div>
			<div class="field"><input type="password" name="password" value="" placeholder="Senha" required></div>
			<div class="field"><button type="submit">Entrar</button></div>
			<div><br></div>
			<div class="field">Precisando de ajuda? <a href="https://wa.me/+5521978907351" target="_blank">Entre em contato</a>.</div>
		</div>
	</form>
</body>
</html>