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

		$this->addJquery();

		$this->addJS(_PS_MODULE_DIR_.'/moonfriend/views/js/jquery.dataTables.js');
		$this->addJS(_PS_MODULE_DIR_.'/moonfriend/views/js/dataTables.bootstrap.js');
		$this->addJS(_PS_MODULE_DIR_.'/moonfriend/views/js/conf.js');

		$this->addCSS(_PS_MODULE_DIR_.'/moonfriend/views/css/jquery.dataTables.css');
		$this->addCSS(_PS_MODULE_DIR_.'/moonfriend/views/css/dataTables.bootstrap.css');
	}
}