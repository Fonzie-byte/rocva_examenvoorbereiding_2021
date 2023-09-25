<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/code/helper.php';

$liked_id = $db->uid_to_id($_GET['liked_type'] . 's', $_GET['liked_uid']);
$user_id = $db->uid_to_id('users', $_GET['liker_uid']);

$db->create('likes', ['liked_id', 'liked_type', 'liker_id'], [$liked_id, $_GET['liked_type'], $user_id]);

header('Location: ' . $project_path . '/views/gamers/show.php?uid=' . $_SESSION['login']['uid']);