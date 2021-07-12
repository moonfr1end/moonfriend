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
	//{"id_module":"13","id_hook":"543","name":"ps_checkpayment","position":"1"},
	//{"id_module":"34","id_hook":"543","name":"ps_wirepayment","position":"2"},
	//{"id_module":"85","id_hook":"543","name":"paypal","position":"3"}

	/* public function addOrder($id_product, $name, $phone, $email)
	{
		$product_name = $this->getProductInfoByID($id_product);
		$pn = $product_name['name'];
		$date = date('Y-m-d H:i:s');
		Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."order_one_click` (`id_order_one_click`, `id_product`, `product_name`, `name`, `email`, `phone`, `date`)
										VALUES (NULL, '$id_product', '$pn', '$name', '$email', '$phone', '$date')");
		return true;
	} */

	public function addOrder($id_product)
	{
		$id_customer = Context::getContext()->customer->id;
		$id_lang = Context::getContext()->language->id;

		$order = new Order();
		$customer = new Customer($id_customer);
		$address = $customer->getAddresses($id_lang);

		$ship_cost = 2.0;
		$date = date('Y-m-d H:i:s');
		$secure_key = md5(uniqid(rand(), true));

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
		$order->payment = 'Payment by check';
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
		//$order->add();

		$product = $cart->getProducts();
		$attribute = Product::getDefaultAttribute($id_product);
		$product_name = $product[0]['name'];
		$product_reference = $product[0]['reference'];
		$shop_id = Context::getContext()->shop->id;

		$order_detail = new OrderDetail();
		$order_detail->id_order = $order->id;
		$order_detail->id_order_invoice = 0;
		$order_detail->id_warehouse = 0;
		$order_detail->id_shop = $shop_id;
		$order_detail->product_id = $id_product;
		$order_detail->product_attribute_id = $attribute;
		$order_detail->id_customization = 0;
		$order_detail->product_name = $product_name;
		$order_detail->product_quantity = 1;
		$order_detail->product_quantity_in_stock = 1;
		$order_detail->product_quantity_refunded = 0;
		$order_detail->product_quantity_return = 0;
		$order_detail->product_quantity_reinjected = 0;
		$order_detail->product_price = $order->total_products;
		$order_detail->reduction_percent = 0.00;
		$order_detail->reduction_amount = 0.000000;
		$order_detail->reduction_amount_tax_incl = 0.000000;
		$order_detail->reduction_amount_tax_excl_= 0.000000;
		$order_detail->group_reduction = 0.00;
		$order_detail->product_quantity_discount = 0.000000;
		$order_detail->product_ean13 = '';
		$order_detail->product_isbn = '';
		$order_detail->product_upc = '';
		$order_detail->product_mpn = '';
		$order_detail->product_reference = $product_reference;
		$order_detail->product_supplier_reference = '';
		$order_detail->product_weight = 0.000000;
		$order_detail->id_tax_rules_group = 0;
		$order_detail->tax_computation_method = 0;
		$order_detail->tax_name = '';
		$order_detail->tax_rate = 0.000;
		$order_detail->ecotax = 0.000000;
		$order_detail->ecotax_tax_rate = 0.000;
		$order_detail->discount_quantity_applied = 0;
		$order_detail->download_hash = '';
		$order_detail->download_nb = 0;
		$order_detail->download_deadline = '0000-00-00 00:00:00';
		$order_detail->total_price_tax_incl = $order->total_products;
		$order_detail->total_price_tax_excl = $order->total_products;
		$order_detail->unit_price_tax_incl = $order->total_products;
		$order_detail->unit_price_tax_excl = $order->total_products;
		$order_detail->total_shipping_price_tax_incl = 0.000000;
		$order_detail->total_shipping_price_tax_excl = 0.000000;
		$order_detail->purchase_supplier_price = 0.000000;
		$order_detail->original_product_price = $order->total_products;
		$order_detail->original_wholesale_price = 0.000000;
		$order_detail->total_refunded_tax_excl = 0.000000;
		$order_detail->total_refunded_tax_incl = 0.000000;
		//$order_detail->add();

		$id_order = $order->id;
		$order_carrier = new OrderCarrier();
		$order_carrier->id_order = $id_order;
		$order_carrier->id_carrier = 2;
		$order_carrier->id_order_invoice = 0;
		$order_carrier->weight = 0.000000;
		$order_carrier->shipping_cost_tax_excl = 2.000000;
		$order_carrier->shipping_cost_tax_incl = 2.000000;
		$order_carrier->tracking_number = '';
		$order_carrier->date_add = $date;
		//$order_carrier->add();

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
		//$cart->delivery_option = '{"3":"2,"}';
		$cart->add();
		$attribute = Product::getDefaultAttribute($id_product);
		Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."cart_product` (`id_cart`, `id_product`, `id_address_delivery`, `id_shop`, `id_product_attribute`, `id_customization`, `quantity`, `date_add`)
										VALUES ('$cart->id', '$id_product', '3', '1', '$attribute', '0', '1', '$date')");


		$customer = new Customer((int)$cart->id_customer);
		$total = $cart->getOrderTotal(true, Cart::BOTH);
		$name = $this->trans('Payments by check', [], 'Modules.Checkpayment.Admin');

		$mailVars = [
			'{check_name}' => Configuration::get('CHEQUE_NAME'),
			'{check_address}' => Configuration::get('CHEQUE_ADDRESS'),
			'{check_address_html}' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS')), ];

		$this->validateOrder((int)$cart->id, (int) Configuration::get('PS_OS_CHEQUE'), $total, $name, null, $mailVars, (int)Context::getContext()->currency->id, false, $customer->secure_key);
		return $cart;
	}

	public function vali()
	{
		$cart = new Cart(43);
		$customer = new Customer((int)Context::getContext()->cart->id_customer);
		$total = $cart->getOrderTotal(true, Cart::BOTH);
		$this->validateOrder((int)$cart->id, 14, $total, 'PayPal', null, array(), null, false, $customer->secure_key);
		return true;
	}

	public function loadProducts($start = 0, $length = 15, $sortby='id_order_one_click', $sortway = 'ASC')
	{
		$count = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'order_one_click`');
		$data = Db::getInstance()->executeS('SELECT *
												FROM `'._DB_PREFIX_.'order_one_click`
												ORDER BY `'.$sortby.'` '.$sortway.'
												LIMIT '.(int)$start.', '.(int)$length);

		return array(
			'recordsTotal' => $count,
			'recordsFiltered' => $count,
			'data' => $data
		);
	}

	public function checkEmail($email) {
		if(Customer::getCustomersByEmail($email)) {
			return true;
		}
		return false;
	}
}