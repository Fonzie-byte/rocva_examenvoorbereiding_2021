<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/views/header.php';

$users = $db->read('users');

?>

	<main>

		<ul>
			<?php foreach ($users as $user) { ?>
				<li><a href="show.php?uid=<?= $user['uid'] ?>"><?= $user['first_name'] ?></a></li>
			<?php } ?>
		</ul>

	</main>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/views/footer.php'; ?>