<?php

if(!defined('_PS_VERSION_'))
	exit;

class Moonfriend extends PaymentModule
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
		return parent::install()
			&& $this->registerHook('displayHeader')
			&& $this->registerHook('displayOneClickOrderButton');
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	public function hookDisplayHeader()
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
		$name = '';
		$phone = '';
		$email = '';
		if($this->context->customer->id !== null) {
			$customer = new Customer($this->context->customer->id);
			$name = $customer->firstname;
			$addresses = $customer->getAddresses($this->context->language->id);
			if($addresses != null) {
				$phone = $addresses[0]['phone'];
			}
			$email = $customer->email;
		}

		$this->context->smarty->assign(array(
			'MSG' => Configuration::get('MOONFRIEND_MSG'),
			'NAME' => $name,
			'PHONE' => $phone,
			'EMAIL' => $email
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

}