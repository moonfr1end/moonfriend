<?php

class MoonFunctions extends PaymentModule
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

		$ship_cost = 0;
		$date = date('Y-m-d H:i:s');
		$secure_key = $customer->secure_key;

		$cart = $this->addCart($address[0]['id_address'], $id_customer, $id_lang, $id_product, $date, $secure_key);

		$order->id_lang = $id_lang;
		$order->id_customer = $id_customer;
		$order->id_address_delivery = $address[0]['id_address'];
		$order->id_address_invoice = $address[0]['id_address'];
		$order->id_cart = $cart->id;
		$order->recyclable = Context::getContext()->cart->recyclable;
		$order->gift_message = Context::getContext()->cart->gift_message;
		$order->id_currency = Context::getContext()->currency->id;
		$order->id_carrier = 2;
		$order->module = 'ps_checkpayment';
		$order->id_shop = Context::getContext()->shop->id;
		$order->id_shop_group = Context::getContext()->shop->id_shop_group;
		$order->current_state = 1;
		$order->payment = 'Оплата чеком';
		$order->total_paid = Product::getPriceStatic($id_product) + $ship_cost;
		$order->total_paid_tax_incl = Product::getPriceStatic($id_product) + $ship_cost;
		$order->total_paid_tax_excl = Product::getPriceStatic($id_product) + $ship_cost;
		$order->total_paid_real = 0;
		$order->total_products = Product::getPriceStatic($id_product);
		$order->total_products_wt = Product::getPriceStatic($id_product);
		$order->total_shipping = $ship_cost;
		$order->total_shipping_tax_incl = $ship_cost;
		$order->total_shipping_tax_excl = $ship_cost;
		$order->conversion_rate=1;
		$order->secure_key = $secure_key;
		$order->reference = Order::generateReference();
		$order->addWs();

		return true;
	}

	public function addCart($id_address, $id_customer, $id_lang, $id_product, $date, $secure_key)
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