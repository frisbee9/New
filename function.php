<?php
function db_connect()
{
	$db_link = mysqli_connect(DBHOST, DBLOGIN, DBPASS, DBNAME);
	if (!$db_link) {
		add_log('Не удалось подключиться к БД: '.mysqli_connect_errno().' - '.mysqli_connect_error());
		return false;
	}
	if (!mysqli_set_charset($db_link, 'utf8')) die('Не удалось установить кодировку utf8');
	mysqli_query($db_link, 'SET SESSION wait_timeout = 18000');
	return $db_link;
}

function query($sql)
{
	global $db_link;
	$res = mysqli_query($db_link, $sql);
	if (!$res) {
		add_log("Ошибка MySQL: ".mysqli_errno($db_link).": ".mysqli_error($db_link)." ($sql)");
		die();
	}
	return $res;
}

function add_log($err)
{
	$date = date('Y-m-d H:i:s');
	$error = $date." - ".$err."\n";
	$file = fopen(dirname(__FILE__).'/log_err.txt', "a+");
	fwrite($file, $error);
	fclose($file);
}

function arr($arr)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

function get_orders() {
	$orders = array();
	$sql = "SELECT o.*, i.`name`, i.`quantity`, i.`price` as price_item FROM `orders` as o LEFT JOIN `items` as i ON (o.`order_id`=i.`order_id`) ORDER BY o.`order_id` DESC";
	$res = query($sql);
	while($r = mysqli_fetch_assoc($res)) {
		$order_id = $r['order_id'];
		if (!isset($orders[$order_id])) {
			$orders[$order_id] = array(
				'address' => $r['address'],
				'city' => $r['city'],
				'phone' => $r['phone'],
				'price' => $r['price'],
			);
		}
		$orders[$order_id]['items'][] = array(
			'name' => $r['name'],
			'quantity' => $r['quantity'],
			'price' => $r['price_item'],
		);
	}
	return $orders;
}

function add_order($order) {
	global $db_link;
	
	$sql = "INSERT INTO `orders` SET 
		`address`='".mysqli_real_escape_string($db_link, $order['address'])."', 
		`city`='".mysqli_real_escape_string($db_link, $order['city'])."', 
		`phone` = '".mysqli_real_escape_string($db_link, $order['phone'])."', 
		`price` = '".$order['price']."' ";
	if (!query($sql)) exit('ERROR SQL: '.$sql);
	$order_id = mysqli_insert_id($db_link);
	
	foreach($order['items'] as $item) {
		$sql = "INSERT INTO `items` SET 
		`order_id` = '".$order_id."', 
		`name` = '".mysqli_real_escape_string($db_link, $item['name'])."', 
		`quantity` = '".$item['quantity']."', 
		`price` = '".$item['price']."' ";
		if (!query($sql)) exit('ERROR SQL: '.$sql);
	}
	
	return $order_id;
}

function del_order($order_id) {
	global $db_link;
	query("DELETE FROM `items` WHERE `order_id` = '".$order_id."'");
	query("DELETE FROM `orders` WHERE `order_id` = '".$order_id."'");
}

?>