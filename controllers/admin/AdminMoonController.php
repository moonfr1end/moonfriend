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
		$this->context->smarty->assign(array());
		$this->setTemplate('moon.tpl');
	}

	public function setMedia($isNewTheme = false)
	{
		parent::setMedia($isNewTheme);
		$this->addJS(_PS_MODULE_DIR_.'/moonfriend/views/js/admincontroller.js');
	}

	public function displayForm()
	{

	}
}