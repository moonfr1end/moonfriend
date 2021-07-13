<?php

class MoonFunctions
{
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

	public function addOrder($id_product)
	{
		$id_customer = Context::getContext()->customer->id;
		$id_lang = Context::getContext()->language->id;

		$order = new Order();
		$customer = new Customer($id_customer);
		$address = $customer->getAddresses($id_lang);

		$date = date('Y-m-d H:i:s');

		$cart = $this->addCart($address[0]['id_address'], $id_customer, $id_lang, $id_product, $date, $customer->secure_key);

		$order->id_lang = $id_lang;
		$order->id_customer = $id_customer;
		$order->id_cart = $cart->id;
		$order->module = 'ps_checkpayment';
		$order->payment = 'Купить в один клик';
		$order->total_paid = $cart->getOrderTotal(true, Cart::BOTH);
		$order->secure_key = $customer->secure_key;
		$order->reference = Order::generateReference();
		$order->addWs();

		return true;
	}

	private function addCart($id_address, $id_customer, $id_lang, $id_product, $date, $secure_key)
	{
		$cart = new Cart;
		$cart->id_address_delivery = $id_address;
		$cart->id_address_invoice = $id_address;
		$cart->id_currency = Context::getContext()->currency->id;
		$cart->id_customer = $id_customer;
		$cart->id_lang = $id_lang;
		$cart->id_shop = Context::getContext()->shop->id;
		$cart->id_shop_group = Context::getContext()->shop->id_shop_group;
		$cart->id_carrier = 2;
		$cart->secure_key = $secure_key;
		$cart->add();

		$attribute = Product::getDefaultAttribute($id_product);
		Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."cart_product` (`id_cart`, `id_product`, `id_address_delivery`, `id_shop`, `id_product_attribute`, `id_customization`, `quantity`, `date_add`)
										VALUES ('$cart->id', '$id_product', '$id_address', '1', '$attribute', '0', '1', '$date')");

		return $cart;
	}

	public function checkEmail($email) {
		if(Customer::getCustomersByEmail($email)) {
			return true;
		}
		return false;
	}
}