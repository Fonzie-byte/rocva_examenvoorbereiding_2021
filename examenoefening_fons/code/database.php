<?php

class Database
{
	private PDO $pdo;

	public function __construct(
		private string $username,
		private string $password,
		private string $host = 'localhost',
		private string $dbname = 'examenoefening_fons',
		private string $charset = 'utf8mb4'
	)
	{
		try
		{
			$dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset";
			$this->pdo = new PDO($dsn, $this->username, $this->password);
		} catch (PDOException $exception)
		{
			exit('Unable to connect. Error message: ' . $exception->getMessage());
		}
	}

	public function create_default_users(): void
	{
		$admin_hashed_password = password_hash('admin', PASSWORD_DEFAULT);
		$user1_hashed_password = password_hash('user', PASSWORD_DEFAULT);
		$user2_hashed_password = password_hash('dummy', PASSWORD_DEFAULT);

		$sql = 'insert into users (uid, first_name, last_name, phone, email, username, password, is_admin)
		values
			(:admin_uid, :admin_first_name, :admin_last_name, :admin_phone, :admin_email, :admin_username, :admin_password, :admin_yes),
			(:user1_uid, :user1_first_name, :user1_last_name, :user1_phone, :user1_email, :user1_username, :user1_password, :admin_no),
			(:user2_uid, :user2_first_name, :user2_last_name, :user2_phone, :user2_email, :user2_username, :user2_password, :admin_no)';

		$this->statement_execute($sql, [
			'admin_yes' => 1,

			'admin_uid' => genuid(),
			'admin_first_name' => 'Admin',
			'admin_last_name' => 'Istrator',
			'admin_phone' => '+31600000001',
			'admin_username' => 'admin',
			'admin_email' => 'admin@example.org',
			'admin_password' => $admin_hashed_password,

			'admin_no' => 0,

			'user1_uid' => genuid(),
			'user1_first_name' => 'Geb',
			'user1_last_name' => 'Ruiker',
			'user1_phone' => '+31600000002',
			'user1_username' => 'user',
			'user1_email' => 'user@example.org',
			'user1_password' => $user1_hashed_password,

			'user2_uid' => genuid(),
			'user2_first_name' => 'Dummy',
			'user2_last_name' => 'Practise',
			'user2_phone' => '+31600000003',
			'user2_username' => 'dummy',
			'user2_email' => 'dummy@example.org',
			'user2_password' => $user2_hashed_password,
		]);
	}

	public function create_default_groups(): void
	{
		$sql = 'insert into `groups` (uid, name, description)
		values
			(:uid1, :name1, :descr1),
			(:uid2, :name2, :descr2)';

		$this->statement_execute($sql, [
			'uid1' => genuid(),
			'name1' => 'Anonymous',
			'descr1' => 'We are Anonymous.
We are Legion.
We do not forgive.
We do not forget.
Expect us.',

			'uid2' => genuid(),
			'name2' => 'Just Good Vibes',
			'descr2' => 'Chill chat for anyone wanting to share memes, I guess.'
		]);
	}

	public function login(string $username, string $password): bool
	{
		$sql = "select uid, first_name, last_name, username, password, is_admin 
		from users
		where username = :username";

		$results = $this->statement_execute($sql, [
			'username' => $username
		])->fetch(PDO::FETCH_ASSOC);

		$hashed_password = $results['password'] ?? null;

		if (!is_array($results) || !password_verify($password, $hashed_password))
		{
			sleep(2);

			return false;
		}

		$_SESSION['login'] = $results;
		$_SESSION['login']['is_admin'] = $results['is_admin'] === '1';

		return true;
	}

	public function get_user_friend_requests($user_id): array
	{
		$sql = 'select u.uid, u.first_name, u.last_name
		from friends f
		left join users u
			on f.friend_a = u.id
		where f.friend_b = :id
			and f.is_accepted = 0';

		return $this->statement_execute($sql, [
			'id' => $user_id
		])->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_user_friends($user_id): array
	{
		$a = $this->query_friends($user_id, 'a');
		$b = $this->query_friends($user_id, 'b');

		return array_merge($a, $b);
	}

	public function get_user_posts($poster_id): array
	{
		if (!is_int($poster_id))
			$poster_id = $this->uid_to_id('users', $poster_id);

		$sql = 'select uid, content, created_at from posts
		where poster_id = :id
			and group_id is null
		order by created_at desc';

		$posts = $this->statement_execute($sql, [
			'id' => $poster_id
		])->fetchAll(PDO::FETCH_ASSOC);

		foreach ($posts as $index => $post)
		{
			foreach ($post as $column => $value)
				$post[$column] = sanitise($value);

			$posts[$index] = $post;
		}

		return $posts;
	}

	public function get_post_likes(int|string $post_id): int
	{
		if (!is_int($post_id))
			$post_id = $this->uid_to_id('posts', $post_id);

		$sql = 'select count(*) as count
		from likes
		where liked_id = :id
			and liked_type = "post"';

		$count = $this->statement_execute($sql, [
			'id' => $post_id
		])->fetch(PDO::FETCH_ASSOC)['count'];

		return $count;
	}

	public function get_post_comments(int|string $post_id): array
	{
		if (!is_int($post_id))
			$post_id = $this->uid_to_id('posts', $post_id);

		$sql = 'select c.uid, c.content, u.uid user_uid, u.first_name, u.last_name
		from comments c
		left join users u on c.commenter_id = u.id
		where c.post_id = :id
		order by c.created_at desc';

		$comments = $this->statement_execute($sql, [
			'id' => $post_id
		])->fetchAll(PDO::FETCH_ASSOC);

		foreach ($comments as $index => $comment)
		{
			foreach ($comment as $column => $value)
				$comment[$column] = sanitise($value);

			$comments[$index] = $comment;
		}

		return $comments;
	}

	public function get_comment_likes(int|string $comment_id): int
	{
		if (!is_int($comment_id))
			$comment_id = $this->uid_to_id('comments', $comment_id);

		$sql = 'select count(*) as count
		from likes
		where liked_id = :id
			and liked_type = "comment"';

		$count = $this->statement_execute($sql, [
			'id' => $comment_id
		])->fetch(PDO::FETCH_ASSOC)['count'];

		return $count;
	}

	public function get_group_members(int|string $group_id): array
	{
		if (!is_int($group_id))
			$group_id = $this->uid_to_id('groups', $group_id);

		$sql = 'select m.first_name, m.last_name, gm.is_moderator 
		from group_members gm
		left join users m
		    on gm.member_id = m.id
		where gm.group_id = :group_id
		order by is_moderator desc';

		$statement = $this->statement_execute($sql, [
			'group_id' => $group_id
		]);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_group_posts(int|string $group_id): array
	{
		if (!is_int($group_id))
			$group_id = $this->uid_to_id('groups', $group_id);

		$sql = 'select u.first_name, u.last_name, p.content, p.created_at
		from posts p
		left join `groups` g
		    on p.group_id = g.id
		left join users u
		    on p.poster_id = u.id
		where p.group_id = :group_id
		order by p.created_at desc';

		$statement = $this->statement_execute($sql, [
			'group_id' => $group_id
		]);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function leave_group(int|string $group_id, int|string $user_id): bool
	{
		if (!is_int($group_id))
			$group_id = $this->uid_to_id('groups', $group_id);
		if (!is_int($user_id))
			$user_id = $this->uid_to_id('users', $user_id);

		$sql = 'delete from group_members where group_id = :group_id and member_id = :user_id';

		$this->statement_execute($sql, [
			'group_id' => $group_id,
			'member_id' => $user_id
		]);

		return true;
	}

	public function delete_friend($friend_a, $friend_b): void
	{
		$sql = 'delete from friends where friend_a = :a and friend_b = :b';

		$this->statement_execute($sql, [
			'a' => $friend_a,
			'b' => $friend_b
		]);
	}

	public function accept_friend(int $friend_a, int $friend_b): void
	{
		$sql = 'update friends set is_accepted = 1 where friend_a = :a and friend_b = :b';

		$this->statement_execute($sql, [
			'a' => $friend_a,
			'b' => $friend_b
		]);
	}

	public function uid_to_id(string $table, string $uid): int
	{
		$table = sanitise($table);

		$result = $this->statement_execute("SELECT `id` FROM `$table` WHERE `uid` = :uid", [
			'uid' => $uid
		])->fetch();

		if (!is_array($result) || empty($result['id']))
			return -1;
//			throw new Exception("Database()->uid_to_id: $uid not found in $table\n");

		return (int)$result['id'];
	}

	public function create(string $table, array $columns, array $values): bool
	{
		if (count($columns) !== count($values))
			return false;

		$column_params = preg_filter('/^/', ':', $columns);
		$column_params = implode(', ', $column_params);

		$columns_str = preg_filter('/^/', '`', $columns);
		$columns_str = preg_filter('/$/', '`', $columns_str);
		$columns_str = implode(', ', $columns_str);

		$prepare_params = [];
		foreach ($values as $index => $value)
		{
			$prepare_params[$columns[$index]] = $value;
		}

		$sql = "insert into `$table` ($columns_str) values ($column_params)";

		$this->statement_execute($sql, $prepare_params);

		return true;
	}

	public function read(string $table, ?string $uid = null): array
	{
		$table = sanitise($table);

		if ($uid === null)
		{
			$sql = "select * from `$table` order by `created_at` desc";

			$statement = $this->statement_execute($sql);

			$results = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach ($results as $index => $result)
				$results[$index] = $this->sanitise_read($result);
		}
		else
		{
			$sql = "select * from `$table` where `uid` = :uid";

			$statement = $this->statement_execute($sql, [
				'uid' => $uid
			]);

			$results = $statement->fetch(PDO::FETCH_ASSOC);

			if (!is_array($results))
				return [];

			$results = $this->sanitise_read($results);
		}

		return $results;
	}

	public function delete(string $table, int|string $id): void
	{
		if (!is_int($id))
			$id = $this->uid_to_id($table, $id);

		$this->statement_execute('delete from :table where uid = :uid', [
			'table' => $table,
			'uid' => $id
		]);
	}

	private function query_friends(int $user_id, string $which): array
	{
		if ($which !== 'a' && $which !== 'b')
			return [];

		$other = $which === 'b' ? 'a' : 'b';

		$sql = "select u.uid, u.first_name, u.last_name
		from friends f
		left join users u
			on f.friend_{$which} = u.id
		where f.friend_{$other} = :id
			and f.is_accepted = 1
		order by u.created_at";

		$statement = $this->statement_execute($sql, [
			'id' => $user_id
		]);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	private function sanitise_read(array $row): array
	{
		unset($row['id']);
		unset($row['password']);

		return array_map('sanitise', $row);
	}

	private function statement_execute(string $sql, array $params = []): PDOStatement
	{
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		return $statement;
	}
}