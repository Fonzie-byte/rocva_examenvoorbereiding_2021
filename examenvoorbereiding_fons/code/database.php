<?php

require_once 'helper.php';

class Database
{
	private PDO $pdo;

	public function __construct(
		private string $db_user,
		private string $db_pass,
		private string $host = 'localhost',
		private string $dbname = 'examenvoorbereiding_fons',
		private string $charset = 'utf8mb4',
	)
	{
		$dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset";
		$this->pdo = new PDO($dsn, $this->db_user, $this->db_pass);
	}

	public function login(string $username, string $password): void
	{
		$sql = 'select type_id, password from users where username = :username';

		$statement = $this->statement_execute($sql, [
			'username' => $username
		]);

		$results = $statement->fetch(PDO::FETCH_ASSOC);

		if (!is_array($results) || !password_verify($password, $results['password']))
			echo 'De gebruikersnaam of het wachtwoord klopte niet. Probeer het nog een keer.<br />';
		else {
			session_start();

			$_SESSION['login']['admin'] = $results['type_id'] === '1';
			$_SESSION['login']['username'] = $username;

			header('Location: /examenvoorbereiding_fons/views/users');
		}
	}

	/* ======== SETUP ======== */
	public function create_default_users(): void
	{
		$admin_password = password_hash('adminadmin', PASSWORD_ARGON2ID);
		$user1_password = password_hash('user1user1', PASSWORD_ARGON2ID);
		$user2_password = password_hash('user2user2', PASSWORD_ARGON2ID);

		$sql = 'insert into users (type_id, username, password)
		values
		       (:admin_type, :admin_user, :admin_pass),
		       (:user_type, :user1_user, :user1_pass),
		       (:user_type, :user2_user, :user2_pass)';

		$this->statement_execute($sql, [
			'admin_type' => 1,
			'admin_user' => 'admin',
			'admin_pass' => $admin_password,

			'user_type' => 2,

			'user1_user' => 'user1',
			'user1_pass' => $user1_password,

			'user2_user' => 'user2',
			'user2_pass' => $user2_password
		]);
	}

	/* ======== USERS ======== */
	public function create_user(int $type_id, string $username, string $password): bool
	{
		if (!$this->user_checks($type_id, $username, $password))
			return false;

		$hashed_password = password_hash($password, PASSWORD_ARGON2ID);

		$sql = 'insert into users (type_id, username, password) values (:type_id, :username, :password)';

		$this->statement_execute($sql, [
			'type_id' => $type_id,
			'username' => $username,
			'password' => $hashed_password
		]);

		return true;
	}

	public function update_user(int $id, int $type_id, string $username, string $password): bool
	{
		if (!$this->user_checks($type_id, $username, $password))
			return false;

		$hashed_password = password_hash($password, PASSWORD_ARGON2ID);

		$sql = 'update users set type_id = :type_id, username = :username, password = :password where id = :id';

		$this->statement_execute($sql, [
			'id' => $id,
			'type_id' => $type_id,
			'username' => $username,
			'password' => $hashed_password
		]);

		return true;
	}

	/* ======== HELPERS ======== */
	private function statement_execute(string $sql, array $params = []): PDOStatement
	{
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		return $statement;
	}

	private function password_strong_enough(string $password): bool
	{
		return strlen($password) >= 8;
	}

	private function value_exists(string $table, string $column, string $new_value): bool
	{
		$sql = "select count(*) `count` from $table where $column = :new_value";

		$result = $this->statement_execute($sql, [
			'new_value' => $new_value
		])->fetch(PDO::FETCH_ASSOC);

		return is_array($result) && $result['count'] > 0;
	}

	private function user_checks(int $type_id, string $username, string $password): bool
	{
		if (!$this->value_exists('user_types', 'id', $type_id)) {
			echo '<p class="error">Kies een geldige rol.</p>';
			return false;
		}
		if ($this->value_exists('users', 'username', $username)) {
			echo '<p class="error">Kies een andere gebruikersnaam.</p>';
			return false;
		}
		if (!$this->password_strong_enough($password)) {
			echo '<p class="error">Kies een sterker wachtwoord.</p>';
			return false;
		}
		// Still here? Then everything's okay!

		return true;
	}

	public function overview(string $table, string $order_by = 'created_at'): array
	{
		$sql = "select * from `$table` order by `$order_by`";

		$results = $this->statement_execute($sql)->fetchAll(PDO::FETCH_ASSOC);

		foreach ($results as $index => $row)
			$results[$index] = sanitize_row($row);

		return $results;
	}

	public function read(string $table, ?int $id = null): array
	{
		$sql = "select * from `$table` where `id` = :id";

		$results = $this->statement_execute($sql, [
			'id' => $id
		])->fetch(PDO::FETCH_ASSOC);

		if (!is_array($results))
			return [];

		return $results;
	}

	public function delete(string $table, int $id): void
	{
		$sql = "delete from $table where id = :id";

		$this->statement_execute($sql, [
			'id' => $id
		]);
	}
}