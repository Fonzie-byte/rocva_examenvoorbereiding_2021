<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenvoorbereiding_fons/views/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (empty($_POST['type_id']))
		$errors[] = 'Kies een geldige rol.';
	if (empty($_POST['username']))
		$errors[] = 'Kies een andere gebruikersnaam.';
	if ($_POST['password'] !== $_POST['password_repeat'])
		$errors[] = 'De wachtwoorden komen niet overeen.';

	if (empty($errors)) {
		if ($db->create_user($_POST['type_id'], $_POST['username'], $_POST['password']))
			header('Location: ' . $project_path . '/views/users');
	}
}

if (is_admin()) {
	foreach ($errors as $error) { ?>
		<p class="error"><?= $error ?></p>
	<?php }    // foreach errors
	?>

	<form method="post">
		<input type="number" name="type_id" placeholder="ID-nummer van rol" value="2" min="1" required/>
		<input type="text" name="username" placeholder="Gebruikersnaam" required autofocus/>
		<input type="password" name="password" placeholder="Wachtwoord" minlength="8" required/>
		<input type="password" name="password_repeat" placeholder="Herhaal wachtwoord" minlength="8" required/>

		<input type="submit" value="Aanmaken">
	</form>

<?php }    // if admin ?>