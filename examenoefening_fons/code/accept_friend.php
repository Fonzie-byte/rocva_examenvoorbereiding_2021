<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/code/helper.php';

$friend_a = $db->uid_to_id('users', $_GET['user_uid']);
$friend_b = $db->uid_to_id('users', $_GET['friend_uid']);

$db->accept_friend($friend_a, $friend_b);

header('Location: ' . $project_path . '/views/gamers/show.php?uid=' . $_SESSION['login']['uid']);