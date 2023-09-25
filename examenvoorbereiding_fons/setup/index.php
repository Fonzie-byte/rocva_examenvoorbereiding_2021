<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenvoorbereiding_fons/environment_variables.php';
require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/code/database.php';

$db = new Database($db_user, $db_pass);

$db->create_default_users();

header('Location: ' . $project_path);