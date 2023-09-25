<?php

$dbh = new PDO(
	'mysql:host=localhost;dbname=ex-sql-injection-002;charset=utf8mb4',
	'root',
	''
);

$user = $_POST['user'];
$pass = $_POST['pass'];

// Hier staat de input nog niet in de string
$sql = 'SELECT pass FROM users WHERE user = :user';

// Dit bereidt hem voor, ook op dat hij nog een :key mist
$statement = $dbh->prepare($sql);
// Dit voert hem uit, met de input erin... Als vaste waarde!
$statement->execute([
	'user' => $user,
]);
// Je kunt nu een user genaamd "user';drop table users;" hebben en het gaat toch goed! :D

$results = $statement->fetch(PDO::FETCH_ASSOC);

if (empty($results) || !password_verify($pass, $results['pass'])) {
	echo '<p>Username or password incorrect.</p>';
	include 'index.php';
	exit;
}

$sql = "SELECT COUNT(*) FROM users";

$statement = $dbh->prepare($sql);
$statement->execute();
$user_count = $statement->fetch();

exit('<span style="color: green">There are ' . $user_count[0] . ' registered users!</span>');
