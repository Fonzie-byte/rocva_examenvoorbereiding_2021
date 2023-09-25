<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/views/header.php';

$groups = $db->read('groups');

?>

	<main>

		<ul>
			<?php foreach ($groups as $group) { ?>
				<li><a href="show.php?uid=<?= $group['uid'] ?>"><?= $group['name'] ?></a></li>
			<?php } ?>
		</ul>

	</main>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/views/footer.php'; ?>