<?php
require_once('../../config/config.inc.php');
require_once('../../init.php');
require_once(_PS_MODULE_DIR_.'/moonfriend/moonFunctions.php');

$obj_mf = new MoonFunctions();

switch(Tools::getValue('action'))
{
	case 'displayForm':
		echo json_encode($obj_mf->getProductInfoByID(Tools::getValue('id_product')), 1);
		break;
	case 'sendForm':
		$id_product = Tools::getValue('id_product');
		$name = Tools::getValue('name');
		$phone = Tools::getValue('phone');
		$email = Tools::getValue('email');

		if(!$obj_mf->checkErrors($name, $phone, $email)) {
			$id_address = $obj_mf->getAddressByPhone($phone);
			$id_customer = $obj_mf->getCustomerID();
			$obj_mf->addOrderOC($id_product, $name, $phone, $email);
			echo $obj_mf->addOrder($id_product, $id_address, $id_customer);
		} else {
			echo json_encode($obj_mf->checkErrors($name, $phone, $email));
		}
		break;
	default:
		break;
}

