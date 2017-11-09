<?php

namespace App\Interfaces;

use App\Models\Product;

interface IProductRepository {

	public function all() : array;

	public function page(int $offset, int $quantity) : array;

	public function byId(int $id) : ?Product;

	public function save(Product $product) : ?Product;

	public function delete(int $id) : bool;

	public function total() : int;

}
