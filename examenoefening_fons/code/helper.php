<?php

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/examenoefening_fons/environment_variables.php';
require_once $_SERVER['DOCUMENT_ROOT'] . $project_path . '/code/database.php';

$db = new Database($db_user, $db_pass);

function format_date(int $unix_time, string $format = '%A %e %B %Y, om %H:%M'): string
{
	return strftime($format, $unix_time ?? time());
}

function dd($value, bool $verbose = true): void
{
	echo '<pre style="background:#222;color:#EEE;font-family:monospace;font-size:16px;padding:8px">';

	if ($verbose)
		var_dump($value);
	else
		print_r($value);

	echo '</pre>';
	exit;
}

function sanitise($input): string
{
	return htmlspecialchars($input, ENT_QUOTES + ENT_HTML5);
}

function random_string(int $length = 32, string $charset = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~ '): string
{
	if ($length <= 0)
		return '';

	$rand_max = mb_strlen($charset) - 1;

	if ($rand_max < 0)
		throw new Exception('charset needs to consist out of at least 1 character');

	$random_string = '';
	for ($s = 0; $s < $length; $s++)
		$random_string .= $charset[random_int(0, $rand_max)];

	return $random_string;
}

function genuid(int $base = 36): string
{
	$time = time();
	$time = base_convert($time, 10, $base);

	$random = random_int(0, pow($base, 2) - 1);
	$random = base_convert($random, 10, $base);
	$random = str_pad($random, 2, '0', STR_PAD_LEFT);

	$uid = substr_replace($time . $random, '-', 4, 0);

	return $uid;
}