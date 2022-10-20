<?php
require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/function.php';

$db_link = db_connect();
if (!$db_link) exit('Не удалось соединится с БД');

$act = isset($_GET['act'])? htmlspecialchars($_GET['act']) : '';

if ($act=='del') {
	$order_id = isset($_GET['order_id'])? (int)$_GET['order_id'] : 0;
	if ($order_id) del_order($order_id);
	header("Location: orders.php");
	exit;
}

$orders = get_orders();
//arr($orders);

?>
<!DOCTYPE html>
<html lang="ua">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Замовлення</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:500,700|Mr+Dafoe|Roboto:400,700&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/styles2.css">
  <script src="js/scripts.js"></script>
   <link rel="shortcut icon" href="assets/icon.png" type="image/x-icon">
</head>
<body style="overflow: auto;">
  <div id="about-bg" class="position-fixed vh-100 vw-100"></div>
  <div class="main">
	<h1>Замовлення</h1>
	<table class="tabl">
	  <tr>
		<th>ID</th>
		<th>Телефон</th>
		<th>Місто</th>
		<th>Адреса</th>
		<th>Сума</th>
		<th></th>
	  </tr>
	  <?php foreach($orders as $order_id => $data) { ?>
	  <tr class="order">
		<td><?=$order_id?></td>
		<td><?=$data['phone']?></td>
		<td><?=$data['city']?></td>
		<td><?=$data['address']?></td>
		<td><?=$data['price']?></td>
		<td> <button><a href="#" onClick="del_order(<?=$order_id?>); return false;">Видалити замовлення</a></button></td>
	  </tr>
	  <tr>
		<td colspan="6">
			<table class="tabl_items">
			  <tr>
				<th>Назва</th>
				<th>Кількість</th>
				<th>Ціна</th>
			  </tr>
			  <?php foreach($data['items'] as $item) { ?>
			  <tr>
				<td><?=$item['name']?></td>
				<td><?=$item['quantity']?></td>
				<td><?=$item['price']?></td>
			  </tr>
			  <?php } ?>
			</table>
		</td>
	  </tr>
	  <?php } ?>
	</table>
	</div>
</body>
</html>