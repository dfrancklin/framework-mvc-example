<?php

namespace App\Controllers;

use FW\Core\Router;
use FW\Core\FlashMessages;
use FW\View\IViewFactory;

use App\Models\Product;
use App\Interfaces\IProductService;
use App\Components\FormComponent;

/**
 * @Controller
 * @Route /products
 * @Authenticate
 */
class ProductsController {

	private $factory;

	private $service;

	private $message;

	public function __construct(IViewFactory $factory, IProductService $service) {
		$this->factory = $factory;
		$this->service = $service;
		$this->message = FlashMessages::getInstance();
	}

	public function products() {
		$quantity = 10;
		$page = $_GET['page'] ?? 1;
		$offset = ($page - 1) * $quantity;
		$products = $this->service->page($offset, $quantity);
		$totalPages = $this->service->totalPages($quantity);

		if ($page > $totalPages) {
			Router::redirect('/products');
		}

		$view = $this->factory::create();
		$view->pageTitle = 'Products';
		$view->products = $products;
		$view->page = (int) $page;
		$view->totalPages = $totalPages;

		return $view->render('products/home');
	}

	/**
	 * @RequestMap /form/{id}
	 */
	public function edit(int $id) {
		$product = $this->service->byId($id);

		if ($product) {
			return $this->form($product);
		} else {
			$this->message->error('No product with the ID ' . $id . ' was found!');
			Router::redirect('/products');
		}
	}

	/**
	 * @RequestMap /form
	 */
	public function create() {
		return $this->form();
	}

	/**
	 * @RequestMethod POST
	 */
	public function save() {
		$product = $this->createProduct();
		$product = $this->service->save($product);

		if ($product) {
			$this->message->info('Product saved!');
		} else {
			$this->message->error('A problem occurred while saving the product!');
		}

		Router::redirect('/products');
	}

	/**
	 * @RequestMap /delete/{id}
	 * @RequestMethod POST
	 */
	public function delete($id) {
		if ($this->service->delete($id)) {
			$this->message->info('Product deleted!');
		} else {
			$this->message->error('A problem occurred while deleting the product!');
		}

		Router::redirect('/products');
	}

	private function form($product = null) {
		$view = $this->factory::create();

		$view->pageTitle = (is_null($product) ? 'New' : 'Update') . ' Product';
		$view->product = $product;
		$view->form = new FormComponent;

		return $view->render('products/form');
	}

	private function createProduct() : Product {
		$properties = ['id', 'name', 'description', 'price', 'quantity'];
		$product = new Product;

		foreach ($properties as $property) {
			if (isset($_POST[$property])) {
				if (is_numeric($_POST[$property])) {
					$product->{$property} = $_POST[$property] + 0;
				} else {
					$product->{$property} = $_POST[$property];
				}
			}
		}

		$picture = '';

		if (isset($_FILES['picture']) && !$_FILES['picture']['error']) {
			$mime = $_FILES['picture']['type'];
			$file = file_get_contents($_FILES['picture']['tmp_name']);
			$picture = sprintf('data:%s;base64,%s', $mime, base64_encode($file));
		} else if ($product->id) {
			$old = $this->service->byId($product->id);
			$picture = $old->picture;
		}

		$product->picture = $picture;

		return $product;
	}

}
