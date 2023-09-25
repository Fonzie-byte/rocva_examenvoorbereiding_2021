<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/code/helper.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<link rel="stylesheet" type="text/css" href="<?= $project_path ?>/styles/main.css"/>
	<title>Fons - Examenoefening</title>
</head>
<body>

<header>
	<nav>
		<ul>
			<?php if (is_array($_SESSION['login'] ?? null)) { ?>
				<li>
					<a href="<?= $project_path ?>/views/gamers/show.php?uid=<?= $_SESSION['login']['uid'] ?>">
						<?= $_SESSION['login']['first_name'] ?>'s page
					</a>
				</li>
				<li>
					<a href="<?= $project_path ?>/views/timeline.php">Timeline</a>
				</li>
				<li><a href="<?= $project_path ?>/views/gamers">Gamers</a></li>
				<li><a href="<?= $project_path ?>/views/squads">Squads</a></li>
				<li><a href="<?= $project_path ?>/code/logout.php">Log out</a></li>
			<?php } ?>
		</ul>
	</nav>
</header>
