<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/views/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	if ($db->login($_POST['username'], $_POST['password']))
		header('Location: ' . $project_path . '/views/gamers/show.php?uid=' . $_SESSION['login']['uid']);
	else
		echo '<p>
	The username or password was incorrect. <br />
	Make sure that you have them right, that you haven\'t made a typo, and then please try again.
</p>';
}

?>

<form method="post">
	<input type="text" name="username" placeholder="Username" required autofocus/> <br/>
	<input type="password" name="password" placeholder="Password" required/> <br/>

	<br/>

	<input type="submit" value="Login">
</form>