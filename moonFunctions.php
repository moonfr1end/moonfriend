<?php

require_once('moonfriend.php');

class MoonFunctions extends Moonfriend
{
	private $errors = Array();

	private $customer;

	private $address;

	public function getProductInfoByID($id_product)
	{
		$image = Image::getCover($id_product);
		$product = new Product($id_product, false, Context::getContext()->language->id);
		$link = new Link;
		$imagePath = $link->getImageLink($product->link_rewrite, $image['id_image'], 'large_default');

		$pn = Db::getInstance()->getValue('SELECT pl.`name`
										FROM `'._DB_PREFIX_.'product` p
										LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
										ON(p.`id_product` = pl.`id_product`)
										WHERE pl.`id_lang` = '.(int)Context::getContext()->language->id.'
										AND p.`id_product` = '.$id_product);

		$price = Product::getPriceStatic($id_product);
		$price = number_format((float)$price, 2, '.', '');

		return array(
			'name' => $pn,
			'price' => $price,
			'image' => $imagePath
		);
	}

	public function addOrder($id_product, $id_address, $id_customer)
	{
		$id_lang = Context::getContext()->language->id;

		$order = new Order();
		$customer = new Customer($id_customer);

		$cart = self::addCart($id_address, $id_customer, $id_lang, $id_product, $customer->secure_key);

		$order->id_lang = $id_lang;
		$order->id_customer = $id_customer;
		$order->id_cart = $cart->id;
		$order->module = 'Moonfriend';
		$order->payment = 'Купить в один клик';
		$order->total_paid = $cart->getOrderTotal(true, Cart::BOTH);
		$order->secure_key = $customer->secure_key;
		$order->reference = Order::generateReference();
		$order->addWs();

		return true;
	}

	private function addCart($id_address, $id_customer, $id_lang, $id_product, $secure_key)
	{
		$cart = new Cart();
		$cart->id_address_delivery = $id_address;
		$cart->id_address_invoice = $id_address;
		$cart->id_currency = Context::getContext()->currency->id;
		$cart->id_customer = $id_customer;
		$cart->id_lang = $id_lang;
		$cart->id_shop = Context::getContext()->shop->id;
		$cart->id_shop_group = Context::getContext()->shop->id_shop_group;
		$cart->id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
		$cart->secure_key = $secure_key;
		$cart->add();

		$attribute = Product::getDefaultAttribute($id_product);
		$cart->updateQty(1, $id_product, $attribute);

		return $cart;
	}

	public function checkErrors($name, $phone, $email)
	{
		$this->checkNameAndPassword($name, $email);
		$this->checkAddress($this->address);
		$this->checkPhone($this->address, $phone);

		return $this->errors;
	}

	private function checkNameAndPassword($name, $email)
	{
		$customer = Customer::getCustomersByEmail($email);
		if($customer != null) {
			if($customer[0]['firstname'] == $name) {
				$this->customer = new Customer($customer[0]['id_customer']);
				$this->address = $this->customer->getAddresses(Context::getContext()->language->id);
				return true;
			}
		}
		$this->errors[] = $this->l('Неправильное имя или email');
		return false;
	}

	private function checkAddress($address)
	{
		if($this->errors == null) {
			if($address != null)
				return true;
			$this->errors[] = $this->l('Добавьте адреса в аккаунта');
			return false;
		}
	}

	private function checkPhone($address, $phone)
	{
		if($this->errors == null) {
			foreach ($address as $value) {
				if($phone == $value['phone'])
					return true;
			}
			$this->errors[] = $this->l('Неправильный телефон');
			return false;
		}
	}

	public function getAddressByPhone($phone)
	{
		foreach ($this->address as $value) {
			if($phone == $value['phone'])
				return $value['id_address'];
		}
	}

	public function getCustomerID()
	{
		return $this->customer->id;
	}

	public function addOrderOC($id_product, $name, $phone, $email)
	{
		$date = date('Y-m-d H:i:s');
		Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."order_one_click` (`id_order_oc`, `id_product`, `name`, `phone`, `email`, `date`) 
										VALUES (NULL, '$id_product', '$name', '$phone', '$email', '$date')");
		return true;
	}
}