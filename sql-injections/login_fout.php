<?php

$dbh = new PDO(
	'mysql:host=localhost;dbname=ex-sql-injection-001;charset=utf8mb4',
	'root',
	''
);

$user = $_POST['user'];
$pass = $_POST['pass'];

// Als de input nu SQL code bevat wordt dat in de string gezet!
$sql = "SELECT * FROM users WHERE user = '$user'";

// Dit prepared dan ook díe SQL code
$statement = $dbh->prepare($sql);
// Die voert het dan ook uit
$statement->execute();
// Hier kun je dus al je data kwijt zijn.

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

exit('<span style="color: darkred">There are ' . $user_count[0] . ' registered users!</span>');
