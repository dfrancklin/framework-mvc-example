<?php

namespace App\Interfaces;

interface IUserRepository {

	function findOne(string $email, string $pass);

}
