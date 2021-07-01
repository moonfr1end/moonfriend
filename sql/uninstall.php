<?php

$sqls = array();

$sqls[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'order_one_click`';

foreach($sqls as $sql)
	if(!Db::getInstance()->execute($sql))
		return false;