<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/code/helper.php';

$user_id = $db->uid_to_id('users', $_POST['commenter_uid']);
$post_id = $db->uid_to_id('posts', $_POST['post_uid']);

$db->create('comments', ['uid', 'content', 'commenter_id', 'post_id'], [genuid(), $_POST['content'], $user_id, $post_id]);

header('Location: ' . $project_path . '/views/gamers/show.php?uid=' . $_POST['commenter_uid']);