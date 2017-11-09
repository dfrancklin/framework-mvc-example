<?php

namespace App\Services;

use App\Models\Product;
use App\Interfaces\IProductService;
use App\Interfaces\IProductRepository;

/**
 * @Service
 */
class ProductService implements IProductService {

	private $repository;

	public function __construct(IProductRepository $repository) {
		$this->repository = $repository;
	}

	public function all() : array {
		return $this->repository->all();
	}

	public function page(int $offset, int $quantity) : array {
		return $this->repository->page($offset, $quantity);
	}

	public function byId(int $id) : ?Product {
		return $this->repository->byId($id);
	}

	public function save(Product $product) : ?Product {
		return $this->repository->save($product);
	}

	public function delete(int $id) : bool {
		return $this->repository->delete($id);
	}

	public function totalPages(int $quantity) : int {
		$total = $this->repository->total();

		return ceil($total / $quantity);
	}

}
