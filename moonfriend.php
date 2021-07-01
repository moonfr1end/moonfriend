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