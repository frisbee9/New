var order = [];

function Pizza(type, size, toppings, quantity, crust, price) {
  this.type = type;
  this.size = size;
  this.toppings = toppings;
  this.crust = crust;
  this.quantity = quantity;
  this.price = price;
}
Pizza.prototype.appendPrice = function () {
  if (this.type == "Папероні") {
    return 400;
  } else if (this.type == "Гавайська") {
    return 350;
  } else if (this.type == "Курячо-грибна") {
    return 450;
  } else if (this.type == "М'ясна") {
    return 400;
  } else if (this.type == "Грибна") {
    return 450;
  } else if (this.type == "Баклажани") {
    return 400;
  }
}

Pizza.prototype.getCost = function () {
  var baseCost = this.appendPrice();
  if (this.size == "Велика") {
    baseCost *= 2.5;
  } else if (this.size == "Середня") {
    baseCost *= 1.6;
  } else if (this.size == "Маленька") {
    baseCost *= 1;
  }
  if (this.toppings == true) {
    baseCost += 150;
  }
  return baseCost * parseInt(this.quantity);
}

Pizza.prototype.addToCart = function () {
  order.push(JSON.stringify(this));
  localStorage.setItem("order", JSON.stringify(order));
  Swal.fire({
    icon: "success",
    text: this.quantity + " " + this.size + " " + this.type + " Піца(и) додана(і) до кошику",
    footer: "<table class='table table-sm table-borderless'><tbody><tr><td class='text-left'>Ціна</td><td class='text-right'>" + this.price + "грн</td></tr></tbody><table>"
  });
}

$().ready(function () {
  $(".pizza").click(function (event) {
    var thisOrder = new Pizza($("h4", this).first().text(), "", "", "", "");
    var random = "<h3>" + thisOrder.type + "</h3><form> <table class='table table-resposive table-borderless text-dark mx-auto'> <tr> <td class='text-left'> <label>Кількість</label> <input class='' type='number' id='quantity' value='1' min='1' style='width: 70px;' required> </td> <td class='text-right'> <label>Розмір</label> <select class='' id='size' required> <option value=''> -- </option> <option value='Велика'>Велика</option> <option value='Середня'>Середня</option> <option value='Маленька'>Маленька</option> </select> </td> </tr> <tr> <td class='text-left'> <label for='crust'>Корочка</label> <select name='crust' id='crust'> <option value=''required>--</option> <option value='Thick'>Товста</option> <option value='Thin'>Тонка</option> </select> </td> <td class='text-right'> <label for='toppings'>Додаткові начинки</label> <input type='checkbox' id='toppings'> </td> </tr> </table> <p id='error' class='text-danger'></p><button class='btn btn-primary pizza' type='button' id='proceed'>Додати до кошику</button></form>";
    Swal.fire({
      html: random,
      showConfirmButton: false
    });
    $("button#proceed").click(function () {
      thisOrder.size = $("#size").val();
      thisOrder.toppings = (document.getElementById("toppings").checked == true) ? true : false;
      thisOrder.crust = $("#crust").val();
      thisOrder.quantity = $("#quantity").val();
      thisOrder.price = thisOrder.getCost();
      if (thisOrder.size == "" || thisOrder.crust == "") {
        $("p#error").text("Не всі обрані параметри");
      } else {
        thisOrder.addToCart();
        $("#cart #cart-num").text(JSON.parse(localStorage.order).length);
      };
    });
  });
  $().ready(function () {
    if (localStorage.getItem("order") == undefined) {
      $("#cart-items").html('<p class="text-center">Ваш кошик пустий</p>');
      $("#checkout").attr("disabled", true).css("cursor", "default");
      $("#clear-cart").attr("disabled", true).css("cursor", "default");
    } else {
      $("#checkout").removeAttr("disabled").css("cursor", "pointer");
      $("#clear-cart").removeAttr("disabled").css("cursor", "pointer");
      $("#cart #cart-num").text(JSON.parse(localStorage.order).length);
      var order = JSON.parse(localStorage.order);
      order.forEach(function (item) {
        var test = '<tr> <td class="text-left w-75">' + JSON.parse(item).quantity + " " + JSON.parse(item).size + " " + JSON.parse(item).type + ' Піца(и)</td> <td class="text-right w-25"> ' + JSON.parse(item).price + '</td> </tr>';
        $("#insertItems").append(test);
      })
    }
  })
  var nums = [];
  for (var i = 0; i < JSON.parse(localStorage.order).length; i++) {
    var num = JSON.parse(JSON.parse(localStorage.order)[i]).price;
    console.log(num)
    nums.push(num);
  };
  total = nums.reduce(function (item, num) {
    return item + num;
  })
  $("#total-amount").text( total + ".00");
  $("button#delivery").click(function () {
    var deliver = $("#deliver").prop("checked");
    var pickup = $("#pickup").prop("checked");
    if (deliver == true) {
      $("#pickup-option").hide();
      $("#deliver-address").fadeIn();
    } else if (pickup == true) {
	  $("#pickup-option").hide();
	  $("#pick-address").fadeIn();
    } else if (deliver != true && pickup != true) {
      $("#error2").text("Будласка оберіть доставку");
    }
  })
  $("button.place-order").first().click(function (event) {
	$("button.place-order").hide();
	$.ajax({
		type: "POST",
		url: "save.php",
		data: "type=pickup&items="+localStorage.order,
		success: function(data){
			Swal.fire({
			  icon: "success",
			  title: "Готово",
			  text: "Ваше замовлення прийнято"
			}).then(function () {
			  localStorage.removeItem("order");
			  location.assign("index.html");
			})
			event.preventDefault();
		}
	});
    
  })
  $(".place-order").submit(function (event) {
    event.preventDefault();
	$(".place-order button").hide();
	var address = $("#address").val();
	var city = $("#city").val();
	var phone = $("#phone").val();

	$.ajax({
		type: "POST",
		url: "save.php",
		data: "type=delivery&address="+address+"&city="+city+"&phone="+phone+"&items="+localStorage.order,
		success: function(data){
		//	alert(data);
			Swal.fire({
			  icon: "success",
			  title: "Готово",
			  text: "Ваше замовлення прийнято"
			}).then(function () {
			  localStorage.removeItem("order");
			  location.assign("index.html");
			})
		}
	});
  })
	
});

function del_order(id)
{
	var msg = "Вы дествительно хотите удалить заказ?";
	if (confirm(msg)) { location.href="?act=del&order_id="+id; }
}