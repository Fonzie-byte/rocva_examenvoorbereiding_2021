<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenvoorbereiding_fons/environment_variables.php';

session_start();

unset($_SESSION['login']);

header('Location: ' . $project_path);