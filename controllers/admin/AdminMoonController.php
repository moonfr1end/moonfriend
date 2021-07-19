<?php

class AdminMoonController extends ModuleAdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function init()
	{
		parent::init();
		$this->bootstrap = true;
	}

	public function initContent()
	{
		parent::initContent();
//		$this->context->smarty->assign(array());
//		$this->setTemplate('moon.tpl');
	}

	public function setMedia($isNewTheme = false)
	{
		parent::setMedia($isNewTheme);
		$this->addJS(_PS_MODULE_DIR_.'/moonfriend/views/js/admincontroller.js');
	}

	public function renderList()
	{
		return $this->initList();
	}

	public function initList()
	{
		$fields_list = array(
			'id_order_oc' => array(
				'title' => $this->module->l('Id order'),
				'width' => 140
			),
			'id_product' => array(
				'title' => $this->l('Id product'),
				'width' => 140
			),
			'name' => array(
				'title' => $this->module->l('Name'),
				'width' => 140
			),
			'phone' => array(
				'title' => $this->module->l('Phone'),
				'width' => 140
			),
			'email' => array(
				'title' => $this->module->l('E-Mail'),
				'width' => 140
			),
			'date' => array(
				'title' => $this->module->l('Date'),
				'width' => 140
			),
		);
		$helper = new HelperList();

		$helper->shopLinkType = '';

		$helper->simple_header = true;

		$helper->identifier = 'id_category';
		$helper->show_toolbar = true;
		$helper->title = 'Order in One-Click List';
		$helper->table = $this->module->name.'_categories';

		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->module->name;

		$list = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."order_one_click`");
		return $helper->generateList($list, $fields_list);
	}
}