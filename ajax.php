<?php
require_once('../../config/config.inc.php');
require_once('../../init.php');
require_once(_PS_MODULE_DIR_.'/moonfriend/moonFunctions.php');

switch(Tools::getValue('action'))
{
	case 'displayForm':
		echo json_encode(MoonFunctions::getProductInfoByID(Tools::getValue('id_product')), 1);
		break;
	case 'sendForm':
		$id_product = Tools::getValue('id_product');
		$name = Tools::getValue('name');
		$phone = Tools::getValue('phone');
		$email = Tools::getValue('email');

		if(!MoonFunctions::checkErrors($name, $phone, $email)) {
			$id_address = MoonFunctions::getAddressByPhone($phone);
			$id_customer = MoonFunctions::getCustomerID();
			echo MoonFunctions::addOrder($id_product, $id_address, $id_customer);
		} else {
			echo json_encode(MoonFunctions::checkErrors($name, $phone, $email));
		}
		break;
	default:
		break;
}

