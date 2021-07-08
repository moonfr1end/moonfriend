<?php
require_once('../../config/config.inc.php');
require_once('../../init.php');
$obj_mf = Module::getInstanceByName('moonfriend');

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
		if($name && $phone && $email && $id_product)
			//echo $obj_mf->addOrder($id_product, $name, $phone, $email);
			echo $obj_mf->addOrder2($id_product);
		else
			echo false;
		break;
	case 'ptable':
		$order = Tools::getValue('order', array());
		$columns = Tools::getValue('columns', array());
		$sortway = $order[0]['dir'];
		$sortby = $columns[$order[0]['column']]['data'];
		echo Tools::jsonEncode($obj_mf->loadProducts(Tools::getValue('start', 0), Tools::getValue('length', 15), $sortby, $sortway));
		break;
	default:
		break;
}