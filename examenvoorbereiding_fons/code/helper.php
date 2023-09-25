<?php

function dd(mixed $value, bool $verbose = true): void
{
	echo '<pre style="background-color:black;color:white;font-size:16px;padding:8px;overflow:scroll">';
	if ($verbose)
		var_dump($value);
	else
		print_r($value);
	echo '</pre>';

	exit;
}

function is_admin(): bool
{
	return array_key_exists('login', $_SESSION) && is_array($_SESSION['login']) && $_SESSION['login']['admin'];
}

function sanitize(string $value): string
{
	return htmlspecialchars($value, ENT_HTML5 + ENT_QUOTES);
}

function sanitize_row(array $row = []): array
{
	foreach ($row as $key => $value) {
		if (!empty($value))
			$row[$key] = sanitize($value);
	}

	return $row;
}