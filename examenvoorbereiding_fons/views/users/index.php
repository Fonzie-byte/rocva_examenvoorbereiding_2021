<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenvoorbereiding_fons/views/header.php';

if (is_admin()) {
	$users = $db->overview('users');

	?>

	<table>
		<tr>
			<th>Rol</th>
			<th>Gebruikernaam</th>
			<th>Aangemaakt op</th>
			<th colspan="2">Beheer</th>
		</tr>

		<?php foreach ($users as $user) { ?>
			<tr>
				<td><?= $user['type_id'] ?></td>
				<td><?= $user['username'] ?></td>
				<td><?= $user['created_at'] ?></td>
				<td><a href="<?= $project_path ?>/views/users/update.php?user_id=<?= $user['id'] ?>">Bewerken</a></td>
				<td><a href="<?= $project_path ?>/code/delete_user.php?user_id=<?= $user['id'] ?>">Verwijderen</a></td>
			</tr>
		<?php }    // foreach users
		?>
	</table>

	<a class="button" href="create.php">Aanmaken</a>

<?php }    // if admin ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/views/footer.php' ?>
