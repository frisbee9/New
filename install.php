<?php
require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/function.php';

$db_link = db_connect();
if (!$db_link) exit('Не удалось соединится с БД');

echo'<br />Пытаемся создать таблицы в БД';

$table = 'orders';
$sql = "CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(5) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `price` float(8,2) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
if (!query($sql)) echo'<br />Не удалось создать таблицу "'.$table.'"'; else echo'<br />Таблица "'.$table.'" создана!';

$table = 'items';
$sql = "CREATE TABLE IF NOT EXISTS `items` (
  `item_id` int(7) NOT NULL AUTO_INCREMENT,
  `order_id` int(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(2) NOT NULL,
  `price` float(8,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `order_id` (`order_id`),
  KEY `order_id_2` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
if (!query($sql)) echo'<br />Не удалось создать таблицу "'.$table.'"'; else echo'<br />Таблица "'.$table.'" создана!';

echo'<br />Все таблицы созданы!';
?>