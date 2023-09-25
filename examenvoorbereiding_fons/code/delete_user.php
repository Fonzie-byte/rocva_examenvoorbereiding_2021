<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenvoorbereiding_fons/environment_variables.php';
require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/code/database.php';

(new Database($db_user, $db_pass))->delete('users', $_GET['user_id']);

header('Location: ' . $project_path . '/views/users');