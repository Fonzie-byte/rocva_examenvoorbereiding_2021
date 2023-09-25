<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/code/helper.php';

$db->leave_group($_GET['uid'], $_SESSION['login']['uid']);

header('Location: ' . $project_path . '/views/squads/show.php?uid=' . $_GET['uid']);