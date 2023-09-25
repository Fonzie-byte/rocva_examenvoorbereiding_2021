<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/views/header.php';

$group = $db->read('groups', $_GET['uid']);
$members = $db->get_group_members($_GET['uid']);
$posts = $db->get_group_posts($_GET['uid']);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$poster_id = $db->uid_to_id('users', $_POST['poster_uid']);
	$group_id = $db->uid_to_id('groups', $_POST['group_uid']);

	$db->create('posts', ['uid', 'poster_id', 'group_id', 'content'], [genuid(), $poster_id, $group_id, $_POST['content']]);

	header('refresh:0');
}

?>

	<div class="sidebar">
		<h2><?= $group['name'] ?></h2>
		<p style="white-space: pre-line"><?= $group['description'] ?></p>

		<a href="<?= $project_path ?>/code/join_group.php?uid=<?= $group['uid'] ?>">Join squad!</a>

		<h3>Members</h3>
		<ul>
			<?php foreach ($members as $member) { ?>
				<li>
					<?= $member['is_moderator'] === '1' ? '<span class="warning">M</span>' : null ?>
					<?= $member['first_name'] . ' ' . $member['last_name'] ?>
				</li>
			<?php } ?>
		</ul>
	</div>

	<main>
		<?php if (true) { ?>
			<form method="post">
				<input type="hidden" name="poster_uid" value="<?= $_SESSION['login']['uid'] ?>"/>
				<input type="hidden" name="group_uid" value="<?= $group['uid'] ?>"/>
				<textarea id="content" name="content"></textarea> <br/>

				<input type="submit" value="Post"/>
			</form>
		<?php } ?>

		<hr/>

		<?php foreach ($posts as $post) { ?>
			<p class="post">
				<b title="<?= $post['first_name'] . ' ' . $post['last_name'] ?>"><?= $post['first_name'] ?></b>
				<i><?= ' op ' . $post['created_at'] ?></i><br/>
				<?= $post['content'] ?> <br/>
			</p>
		<?php } ?>
	</main>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/views/footer.php'; ?>