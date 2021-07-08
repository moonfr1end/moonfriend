<?php

if(!defined('_PS_VERSION_'))
	exit;

class Moonfriend extends Module
{
	public function __construct()
	{
		$this->name = 'moonfriend';
		$this->author = 'Moon';
		$this->version = '1.0.0';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Moonfriend');
		$this->description = $this->l('Quick order in one click!');
		$this->ps_versions_compliancy = array('min' => '1.7.7.0', 'max' => '1.7.7.99');
	}

	public function install()
	{
		include_once($this->local_path.'sql/install.php');
		return parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('displayOneClickOrderButton')
			&& $this->installModuleTab();
	}

	public function uninstall()
	{
		include_once($this->local_path.'sql/uninstall.php');
		return parent::uninstall();
	}

	public function hookHeader()
	{
		Media::addJsDef(array(
			'mf_ajax' => $this->_path.'ajax.php'
		));
		$this->context->controller->addCSS(array(
			$this->_path.'views/css/moonfriend.css'
		));
		$this->context->controller->addJS(array(
			$this->_path.'views/js/moonfriend.js'
		));
	}

	public function hookDisplayOneClickOrderButton()
	{
		$this->context->smarty->assign(array(
			'MSG' => Configuration::get('MOONFRIEND_MSG')
		));
		return $this->display(__FILE__, 'views/templates/hook/oneClickButton.tpl');
	}

	public function getContent()
	{
		if(Tools::isSubmit('save-msg')) {
			$msg = Tools::getValue('print');
			Configuration::updateValue('MOONFRIEND_MSG', $msg);
		}
		$this->context->smarty->assign(array(
			'MOONFRIEND_MSG' => Configuration::get('MOONFRIEND_MSG')
		));
		return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
	}

	public function getProductInfoByID($id_product)
	{
		$image = Image::getCover($id_product);
		$product = new Product($id_product, false, $this->context->language->id);
		$link = new Link;
		$imagePath = $link->getImageLink($product->link_rewrite, $image['id_image'], 'large_default');

		$pn = Db::getInstance()->getValue('SELECT pl.`name`
										FROM `'._DB_PREFIX_.'product` p
										LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
										ON(p.`id_product` = pl.`id_product`)
										WHERE pl.`id_lang` = '.(int)$this->context->language->id.'
										AND p.`id_product` = '.$id_product);

		$price = Product::getPriceStatic($id_product);
		$price = number_format((float)$price, 2, '.', '');
		return array(
			'name' => $pn,
			'price' => $price,
			'image' => $imagePath
		);
	}

	public function installModuleTab()
	{
		$tab = new Tab;
		foreach (Language::getLanguages() as $lang)
		{
			$tab->name[$lang['id_lang']] = $this->l('Заказы в один клик');
		}
		$tab->class_name = 'AdminMoon';
		$tab->module = $this->name;
		$tab->id_parent = Tab::getIdFromClassName('AdminParentOrders');
		$tab->active = 1;
		$tab->add();
		return true;
	}

	public function addOrder($id_product, $name, $phone, $email)
	{
		$product_name = $this->getProductInfoByID($id_product);
		$pn = $product_name['name'];
		$date = date('Y-m-d H:i:s');
		Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."order_one_click` (`id_order_one_click`, `id_product`, `product_name`, `name`, `email`, `phone`, `date`) 
										VALUES (NULL, '$id_product', '$pn', '$name', '$email', '$phone', '$date')");
		return true;
	}

	public function addOrder2($id_product)
	{
//		$product_name = $this->getProductInfoByID($id_product);
//		$pn = $product_name['name'];
//		$date = date('Y-m-d H:i:s');

		$id_customer = $this->context->customer->id;
		$id_lang = $this->context->language->id;

		$order = new Order;
		$customer = new Customer($id_customer);
		$address = $customer->getAddresses($id_lang);

		$cart = $this->addCart($address[1]['id_address'], $id_customer, $id_lang, $id_product);

		$order->id_lang = $id_lang;
		$order->id_customer = $id_customer;
		$order->id_address_delivery = $address[1]['id_address'];
		$order->id_address_invoice = $address[1]['id_address'];
		$order->id_cart = $cart->id;
		$order->recyclable = 0;
		$order->gift_message = '';
		$order->shipping_number = '';
		$order->id_currency = $this->context->currency->id;
		$order->id_carrier = 2;
		$order->module = 'ps_checkpayment';
		$order->id_shop = $this->context->shop->id;
		$order->id_shop_group = $this->context->shop->id_shop_group;
		$order->current_state = 1;
		$order->payment = 'Payment by check';
		$ship_cost = 2.0;
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
		$order->secure_key = 'b44a6d9efd7a0076a0fbce6b15eaf3b1';
		$order->reference = Order::generateReference();
		$order->add();
		$product = $cart->getProducts();
		$attribute = Product::getDefaultAttribute($id_product);
		$product_name = $product[0]['name'];
		$product_reference = $product[0]['reference'];
		$shop_id = $this->context->shop->id;
		$date = date('Y-m-d H:i:s');
		Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."order_detail` (`id_order_detail`, `id_order`, `id_order_invoice`, `id_warehouse`, `id_shop`, `product_id`, `product_attribute_id`, `id_customization`, `product_name`, `product_quantity`, `product_quantity_in_stock`, `product_quantity_refunded`, `product_quantity_return`, `product_quantity_reinjected`, `product_price`, `reduction_percent`, `reduction_amount`, `reduction_amount_tax_incl`, `reduction_amount_tax_excl`, `group_reduction`, `product_quantity_discount`, `product_ean13`, `product_isbn`, `product_upc`, `product_mpn`, `product_reference`, `product_supplier_reference`, `product_weight`, `id_tax_rules_group`, `tax_computation_method`, `tax_name`, `tax_rate`, `ecotax`, `ecotax_tax_rate`, `discount_quantity_applied`, `download_hash`, `download_nb`, `download_deadline`, `total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`, `total_shipping_price_tax_incl`, `total_shipping_price_tax_excl`, `purchase_supplier_price`, `original_product_price`, `original_wholesale_price`, `total_refunded_tax_excl`, `total_refunded_tax_incl`)
										VALUES (NULL, '$order->id', '0', '0', '$shop_id', '$id_product', '$attribute', '0', '$product_name', '1', '1', '0', '0', '0', '$order->total_products', '0.00', '0.000000', '0.000000', '0.000000', '0.00', '0.000000', '', '', '', '', '$product_reference', '', '0.000000', '0', '0', '', '0.000', '0.000000', '0.000', '0', '', '0', '0000-00-00 00:00:00', '$order->total_products', '$order->total_products', '$order->total_products', '$order->total_products', '0.000000', '0.000000', '0.000000', '$order->total_products', '0.000000', '0.000000', '0.000000')");

		$id_order = $order->id;
		Db::getInstance()->execute("INSERT INTO `order_carrier` (`id_order_carrier`, `id_order`, `id_carrier`, `id_order_invoice`, `weight`, `shipping_cost_tax_excl`, `shipping_cost_tax_incl`, `tracking_number`, `date_add`) 
										VALUES (NULL, '$id_order', '2', '0', '0.000000', '2.000000', '2.000000', '', '$date')");
		return true;
	}

	public function addCart($id_address, $id_customer, $id_lang, $id_product)
	{
		$cart = new Cart;
		$cart->id_address_delivery = $id_address;
		$cart->id_address_invoice = $id_address;
		$cart->id_currency = $this->context->currency->id;
		$cart->id_customer = $id_customer;
		$cart->id_lang = $id_lang;
		$cart->id_shop = $this->context->shop->id;
		$cart->id_shop_group = $this->context->shop->id_shop_group;
		$cart->id_carrier = 2;
		$cart->secure_key = 'b44a6d9efd7a0076a0fbce6b15eaf3b1';
		$cart->id_guest = 1;
		$cart->delivery_option = '{"3":"2,"}';
		$cart->add();
		$date = date('Y-m-d H:i:s');
		$attribute = Product::getDefaultAttribute($id_product);
		Db::getInstance()->execute("INSERT INTO `cart_product` (`id_cart`, `id_product`, `id_address_delivery`, `id_shop`, `id_product_attribute`, `id_customization`, `quantity`, `date_add`)
										VALUES ('$cart->id', '$id_product', '3', '1', '$attribute', '0', '1', '$date')");
		return $cart;
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
}