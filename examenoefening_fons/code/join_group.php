<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/code/helper.php';

$user_id = $db->uid_to_id('users', $_SESSION['login']['uid']);
$group_id = $db->uid_to_id('groups', $_GET['uid']);

$db->create('group_members', ['group_id', 'member_id'], [$group_id, $user_id]);

header('Location: ' . $project_path . '/views/squads/show.php?uid=' . $_GET['uid']);