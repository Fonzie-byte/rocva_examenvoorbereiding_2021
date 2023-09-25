<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/code/helper.php';

$db->create_default_users();
$db->create_default_groups();

require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/code/logout.php';