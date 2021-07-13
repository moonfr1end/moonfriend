<?php
require_once('../../config/config.inc.php');
require_once('../../init.php');
require_once(_PS_MODULE_DIR_.'/moonfriend/moonFunctions.php');

$obj_mf = new MoonFunctions();

switch(Tools::getValue('action'))
{
	case 'displayForm':
		echo json_encode($obj_mf->getProductInfoByID(Tools::getValue('id_product')));
		break;
	case 'sendForm':
		$id_product = Tools::getValue('id_product');
		$name = Tools::getValue('name');
		$phone = Tools::getValue('phone');
		$email = Tools::getValue('email');
		if($name && $phone && $email && $id_product) {
			if (Context::getContext()->customer->id !== null) {
				$check = $obj_mf->checkEmail($email);
				$cus = Customer::getCustomersByEmail($email);
				if($check) {
					if($cus[0]['id_customer'] == Context::getContext()->customer->id) {
						if(mb_strtoupper($cus[0]['firstname']) == mb_strtoupper($name)) {
							//$obj_mf->addOrder($id_product);
							$obj_mf->addOrder($id_product);
							echo 4;
						} else {
							echo 3;
						}
					} else {
						echo 2;
					}
				} else {
					echo 2;
				}
			} else {
				echo 1;
			}
		} else {
			echo 0;
		}
		break;
	default:
		break;
}

