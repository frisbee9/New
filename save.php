<?php
require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/function.php';
arr($_POST);
$db_link = db_connect();
if (!$db_link) exit('Не удалось соединится с БД');

try {
	$type = isset($_POST['type'])? htmlspecialchars($_POST['type']) : '';
	$address = isset($_POST['address'])? htmlspecialchars($_POST['address']) : '';
	$city = isset($_POST['city'])? htmlspecialchars($_POST['city']) : '';
	$phone = isset($_POST['phone'])? htmlspecialchars($_POST['phone']) : '';
	
	if ($type!='pickup') {
		if (empty($address)) throw new Exception('EMPTY_ADDRESS');
		if (empty($city)) throw new Exception('EMPTY_CITY');
		if (empty($phone)) throw new Exception('EMPTY_PHONE');
	}
	
	$order = array(
		'address' => $address,
		'city' => $city,
		'phone' => $phone,
		'price' => 0,
	);
	if (!isset($_POST['items'])) throw new Exception('NOT_POST_ITEMS');
	$items = json_decode($_POST['items'], true);
	foreach($items as $item_json) {
		$item = json_decode($item_json, true);
		
		$name = $item['size'].' '.$item['type'];
		if ($item['crust']=='Thick') $name .= ', Товста';
		elseif ($item['crust']=='Thin') $name .= ', Тонка';
		if ($item['toppings']==1) $name .= ', Додаткові начинки';
		
		$order['price'] += (float)$item['price'];
		$info = array(
			'quantity' => (int)$item['quantity'],
			'name' => $name,
			'price' => (float)$item['price'],
		);
		$order['items'][] = $info;
		//arr($item);
	}
	//arr($order);
	$order_id = add_order($order);
 
} catch (Exception $e) {// обработка исключения
	$err = $e->getMessage();
	add_log('Error: '.$err);
}

?>

