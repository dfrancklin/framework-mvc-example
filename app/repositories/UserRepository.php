<?php

namespace App\Repositories;

use \App\Models\User;
use \App\Interfaces\IUserRepository;

/**
 * @Repository
 */
class UserRepository implements IUserRepository {

	private $usersTable = [];

	public function __construct() {
		$this->usersTable = $this->populateTable();
	}

	public function findOne(string $email, string $pass) {
		foreach ($this->usersTable as $user) {
			if ($user->email === $email && $user->pass === $pass) {
				return $user;
			}
		}
	}

	private function createUser(array $info) : User {
		$user = new User;

		$properties = ['id', 'name', 'email', 'pass', 'roles'];

		foreach ($properties as $property) {
			$user->{$property} = $info[$property];
		}

		return $user;
	}

	private function populateTable() : array {
		$table = [];

		$table[] = $this->createUser([
			'id' => 1,
			'name' => 'Diego Francklin',
			'email' => 'dfrancklin23@gmail.com',
			'pass' => '123',
			'roles' => ['ADMIN', 'USER'],
		]);

		$table[] = $this->createUser([
			'id' => 2,
			'name' => 'admin',
			'email' => 'admin@email.com',
			'pass' => '1234',
			'roles' => ['ADMIN'],
		]);

		foreach(range(3, 10) as $id) {
			$table[] = $this->createUser([
				'id' => $id,
				'name' => 'System User #' . $id,
				'email' => 'system.user.' . $id . '@email.com',
				'pass' => 'xpto@' . $id,
				'roles' => ['USER'],
			]);
		}

		return $table;
	}

}
