<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/views/header.php';

$user_id = $db->uid_to_id('users', $_GET['uid']);

$user = $db->read('users', $_GET['uid']);

$friend_requests = $db->get_user_friend_requests($user_id);
$friends = $db->get_user_friends($user_id);

$posts = $db->get_user_posts($user_id);

$owner = $user['uid'] === $_SESSION['login']['uid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $owner)
{
	$poster_id = $db->uid_to_id('users', $_POST['poster_uid']);

	if ($db->create('posts', ['uid', 'poster_id', 'content'], [genuid(), $poster_id, $_POST['content']]))
		header('refresh: 0');
}

?>

	<div class="sidebar">
		<h2><b><?= $user['first_name'] . ' ' . $user['last_name'] ?></b></h2>

		<table>
			<?php if ($user['is_admin'] === '1') { ?>
				<tr>
					<td class="warning" colspan="2">This is a sitemoderator!</td>
				</tr>
			<?php } ?>

			<tr>
				<td class="accented">@:</td>
				<td><?= $user['email'] ?></td>
			</tr>

			<tr>
				<td class="accented">T:</td>
				<td><?= $user['phone'] ?></td>
			</tr>
		</table>

		<h3>Friends</h3>
		<?php if ($user['uid'] !== $_SESSION['login']['uid']) { ?>
			<a href="<?= $project_path ?>/code/add_friend.php?user_uid=<?= $_SESSION['login']['uid'] ?>&friend_uid=<?= $user['uid'] ?>">
				Send friend request
			</a> <br/>
		<?php } ?>

		<ul>
			<?php foreach ($friend_requests as $request) { ?>
				<li>
					<span class="warning"><?= $request['first_name'] . ' ' . $request['last_name'] ?></span>

					<a href="<?= $project_path ?>/code/accept_friend.php?user_uid=<?= $request['uid'] ?>&friend_uid=<?= $_SESSION['login']['uid'] ?>">Accept</a>
					<a href="<?= $project_path ?>/code/delete_friend.php?user_uid=<?= $request['uid'] ?>&friend_uid=<?= $_SESSION['login']['uid'] ?>">Reject</a>
				</li>
			<?php } ?>

			<?php
			foreach ($friends as $friend)
			{ ?>
				<li>
					<?= $friend['first_name'] . ' ' . $friend['last_name'] ?>

					<a href="<?= $project_path ?>/views/gamers/show.php?uid=<?= $friend['uid'] ?>">Visit</a>
					<?php if ($owner) { ?>
						<a href="<?= $project_path ?>/code/delete_friend.php?user_uid=<?= $_SESSION['login']['uid'] ?>&friend_uid=<?= $user['uid'] ?>">Remove</a>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>

	</div>

	<main>
		<?php if ($owner) { ?>
			<form method="post">
				<input type="hidden" name="poster_uid" value="<?= $_SESSION['login']['uid'] ?>"/>
				<textarea id="content" name="content"></textarea> <br/>

				<input type="submit" value="Post"/>
			</form>
		<?php } ?>

		<hr/>

		<?php

		foreach ($posts as $post)
		{
			$post['likes'] = $db->get_post_likes($post['uid']);
			$comments = $db->get_post_comments($post['uid']);

			?>
			<form method="post" action="<?= $project_path ?>/code/add_comment.php">
				<input type="hidden" name="commenter_uid" value="<?= $_SESSION['login']['uid'] ?>"/>
				<input type="hidden" name="post_uid" value="<?= $post['uid'] ?>"/>

				<p class="post">
				<span class="content">
					<?= $post['content'] ?>
				</span>

					<a class="button"
					   href="<?= $project_path ?>/code/add_like.php?liked_uid=<?= $post['uid'] ?>&liked_type=post&liker_uid=<?= $_SESSION['login']['uid'] ?>">
						&#9650; <?= $post['likes'] ?>
					</a>
					<br/>

					<i><?= $post['created_at'] ?></i>
					<br/>

					<input type="text" name="content" placeholder="Comment"/>
					<input type="submit" value="Post"/>
					<br/>

					<?php

					foreach ($comments as $comment)
					{
						$comment['likes'] = $db->get_comment_likes($comment['uid']);

						?>
						<b
						title="<?= $comment['first_name'] . ' ' . $comment['last_name'] ?>"><?= $comment['first_name'] . ': ' ?></b><?= $comment['content'] ?>

						<a class="button"
						   href="<?= $project_path ?>/code/add_like.php?liked_uid=<?= $comment['uid'] ?>&liked_type=comment&liker_uid=<?= $_SESSION['login']['uid'] ?>">
							&#9650; <?= $comment['likes'] ?>
						</a>

						<br/>
					<?php } ?>
				</p>
			</form>
		<?php } ?>
	</main>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/views/footer.php'; ?>