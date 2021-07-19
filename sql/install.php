<?php

$sqls = array();

$sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'order_one_click`(
	`id_order_oc` INT(10) AUTO_INCREMENT,
	`id_product` INT(10),
	`name` VARCHAR(255),
	`phone` VARCHAR(255),
    `email` VARCHAR(255),
    `date` DATETIME NOT NULL,
	PRIMARY KEY(`id_order_oc`)
) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';

foreach($sqls as $sql)
	if(!Db::getInstance()->execute($sql))
		return false;