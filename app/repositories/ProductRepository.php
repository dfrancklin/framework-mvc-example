<?php

namespace App\Repositories;

use App\Interfaces\IProductRepository;
use App\Models\Product;

/**
 * @Repository
 */
class ProductRepository implements IProductRepository {

	const RESOURCE = __DIR__ . '/../models/Product.data.json';

	private static $table;

	public function __construct() {
		$this->loadTable();
	}

	public function all() : array {
		return self::$table;
	}

	public function page(int $offset, int $quantity) : array {
		$page = [];

		for ($i = $offset; isset(self::$table[$i]) && $quantity; $i++, --$quantity) {
			$page[] = self::$table[$i];
		}

		return $page;
	}

	public function byId(int $id) : ?Product {
		$list = array_filter(self::$table, function($item) use ($id) {
			return $item->id === $id;
		});

		if (empty($list)) {
			return null;
		}

		return (array_values($list))[0];
	}

	public function save(Product $product) : ?Product {
		if (!$product->id) {
			$product = $this->create($product);
		} else {
			$product = $this->update($product);
		}

		if ($this->updateTable()) {
			return $product;
		}

		return null;
	}

	public function delete(int $id) : bool {
		$size = count(self::$table);

		$newTable = array_filter(self::$table, function($item) use ($id) {
			return $item->id !== $id;
		});

		$newSize = count($newTable);

		if ($size - 1 === $newSize) {
			self::$table = $newTable;

			if ($this->updateTable()) {
				return true;
			}
		}

		return false;
	}

	public function total() : int {
		return count(self::$table);
	}

	private function loadTable() {
		$json = file_get_contents(self::RESOURCE);
		$list = json_decode($json);

		self::$table = [];

		foreach ($list as $p) {
			self:: $table[] = $this->createProduct($p);
		}
	}

	private function updateTable() {
		return file_put_contents(self::RESOURCE, json_encode(self::$table));
	}

	private function createProduct($p) : Product {
		$product = new Product;

		$properties = ['id', 'name', 'description', 'picture', 'price', 'quantity'];

		foreach ($properties as $property) {
			$product->{$property} = $p->{$property};
		}

		return $product;
	}

	private function create(Product $product) : Product {
		$product->id = $this->nextId();
		self::$table[$key] = $product;

		return $product;
	}

	private function update(Product $product) : Product {
		foreach (self::$table as $key => $item) {
			if ($item->id === $product->id) {
				self::$table[$key] = $product;
				break;
			}
		}

		return $product;
	}

	private function nextId() {
		return $this->maxId() + 1;
	}

	private function maxId() {
		$max = 0;

		foreach (self::$table as $item) {
			if ($item->id > $max) {
				$max = $item->id;
			}
		}

		return $max;
	}

}
