<?php
error_reporting(-1);
/*
 * Copyright (c) 2015 MegaX <info@megax.be>
 */
 
$bookwitdh= "25%";
$bookheight= "300px";
$bidaskwitdh ="45%";
$tablewitdh ="50%";
?>
<html>
<head>
	<style>
		div.qr {
		 margin-right: auto; 
		 width: <?php echo $bookwitdh; ?>;	
		}
		div.orders {
		 margin-right: auto; 
		 /* margin-left: auto; */
		 width: <?php echo $bookwitdh; ?>;	
		}
		table.bids {
		  position:relative;
		  float:left;
		  display: table;
		  width: 100%;
		}
		table.bids thead, table.bids tbody {
			float: left;
			width: 100%;
		}
		table.bids tbody {
			overflow: auto;
		  height: <?php echo $bookheight; ?>;
		}
		table.bids tr {
			width: 100%;
			display: table;
			text-align: left;
		}
		table.bids th, table.bids td {
			width: <?php echo $bookwitdh; ?>;
		}
		table.asks {
			position:relative;
			float:left;
			display: table;
			width: 100%;
		}
		table.asks thead, table.asks tbody {
			float: left;
			width: 100%;
		}
		table.asks tbody {
			overflow: auto;
			height: <?php echo $bookheight; ?>;
		}
		table.asks tr {
			width: 100%;
			display: table;
			text-align: left;
		}
		table.asks th, table.asks td {
			width: <?php echo $bookwitdh; ?>;
		}
		div.bids{
		  width:<?php echo $bidaskwitdh; ?>;
		  float:right;
		}
		div.asks{
		  width:<?php echo $bidaskwitdh; ?>;
		  float:left;
		}
		div.bids td {
		  width:<?php echo $tablewitdh;?>;
		  float:centert;
		  text-align: center;
		}
		div.asks td{
		  width:<?php echo $tablewitdh;?>;
		  float:center;
		  text-align: center;
		}
		td.asks {
		  background:#FFCCCC;
		}
		td.bids {
		  background:#66FFCC;
		}		
		th {
		  text-align:center;
		}
		.stripe-r {
		  color: lightblue;
		  background: repeating-linear-gradient(
			45deg,
			#CC6666,
			#CC6666 10px,
			#CC0000 10px,
			#CC0000 20px
		  );
		}		
		.stripe-g {
		  color: darktblue;
		  background: repeating-linear-gradient(
			45deg,
			#33CC00,
			#33CC00 10px,
			#33FFCC 10px,
			#33FFCC 20px
		  );
		}	
</style>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script type="text/javascript">
/* Needs further investigation: not working somehow.
$('.asks').click(function(asks){   
    var price = $(asks.target).text();
    var elem = document.getElementById("askprice");
    elem.value = price;
})
$('.bids').click(function(bids){   
    var price = $(bids.target).text();
    var elem = document.getElementById("bidprice");
    elem.value = price;
})
$('.balancebtc').click(function(balancebtc){   
    var balance = $(balancebtc.target).text();
    var elem = document.getElementById("amountbtc");
    elem.value = balance;
})
$('.balanceeur').click(function(balanceeur){   
    var balance = $(balanceeur.target).text();
    var elem = document.getElementById("amounteur");
    elem.value = balance;
}) 
*/
</script>
</head>
<body>
<?php

 /* The API class */
require_once('CleverAPIClientV1.class.php');

 /* Your API credentials */
require_once('CleverAPIClientAuth.php');

 /* Load the API */
$cleverAPI = new CleverAPIClientV1($key, $secret);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $verifyCertificate = false;
} else {
    $verifyCertificate = true;
}

$ticker = $cleverAPI->getTicker();
$btcask = $ticker['ask'];
echo "Ask 1&#xE3F; = ".$btcask."&euro;"; //€
echo "<br>";
$btcbid = $ticker['bid'];
echo "Bid 1&#xE3F; = ".$btcbid."&euro;"; //€
echo "<br>";

if (isset($_GET['amount'])) {
	$amount = $_GET['amount'];
}else{
	$amount = '0';
}
if (isset($_GET['price'])) {
	$price = $_GET['price']; 
}else{
	$price = '0';
}
if (isset($_GET['type'])) {
	$type = $_GET['type']; 
}
if (isset($_GET['amount']) && isset($_GET['price']) && isset($_GET['type'])) {
	$order= ($price * $amount);
	if ($type=='bid'){
		$amount = $_GET['amount'];
		$price =  $_GET['price'];
		$type = $_GET['type'];
		echo "<br>";
		$cleverAPI->createLimitedOrder($type, $amount, $price);
		echo ucfirst($type)." order placed for ".$order."&euro;";
		echo "<br>";		
	}
	if ($type=='ask'){
		$amount = $_GET['amount'];
		$price =  $_GET['price'];
		$type = $_GET['type'];		
		echo "<br>";
		$cleverAPI->createLimitedOrder($type, $amount, $price);
		echo ucfirst($type)." order placed for ".$order."&euro;";
		echo "<br>";
	}
}

echo "<br>";

 /* Get bitcoin deposit address */
$bitcoinAddress = $cleverAPI->getBitcoinDepositAddress();
$bitcoindepositstring= "bitcoin:".$bitcoinAddress['address'];
echo '<a href="'.$bitcoindepositstring.'">'.$bitcoinAddress['address'].'</a>';

echo '<div class="qr"><a href="'.$bitcoindepositstring.'"><img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$bitcoindepositstring.'%2F&choe=UTF-8" title="'.$bitcoindepositstring.'" /></a></div>';

echo "<br>";
$cleverWallets = $cleverAPI->getWallets();

$BTC=$cleverWallets[1];
echo "<div class=\"balancebtc\">&#xE3F;itcoin wallet balance: ".$BTC['balance']." &#xE3F;</div>";
echo "<br>";

$EUR=$cleverWallets[0];
echo "<div class=\"balanceeur\">&euro;uro wallet balance: ".$EUR['balance']." &euro;</div>";
echo "<br>";
echo "<br>";

 /* Load the Orderbook */
$group = 0;
$book = $cleverAPI->getOrderBook($group);

echo "<div class=\"orders\">";
	echo "<div class=\"bids\">";
			echo "<table class=\"bids\" id=\"orderbids\">";
				echo "<thead class=\"stripe-g\">";
					echo "<tr colspan=\"2\">";
						echo "<th class=\"bids\">";
							echo "Bids";
						echo "</th>";	
					echo "</tr>";
					echo "<tr>";
						echo "<th class=\"bids\">";
							echo "Price";
						echo "</th>";
						echo "<th class=\"bids\">";
							echo "Volume";
						echo "</th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
			foreach($book['bids'] as $key=>$value) {
					echo "<tr>";		
						echo "<td class=\"bids\" id=\"orderbids\">";
							/* Load the Orderbook prices */		
							print_r($book['bids'][$key]['0']);
						echo "</td>";
						echo "<td class=\"bids\" id=\"volume\">";
							/* Load the Orderbook Volumes */		
							print_r($book['bids'][$key]['1']);	
						echo "</td>";
					echo "</tr>";		
			}
				echo "</tbody>";
			echo "</table>";
		echo '<div class="orderbid">';
		echo '<br>';
			echo '<form action='.$PHP[self].'>';
					echo 'Amount: <input type="text" name="amount" id="amounteur" class="amounteur" value="0.00"></input><br>';
					echo 'Price: <input type="text" name="price" id="bidprice" value='.$btcbid.'></input><br>';
					echo '<input type="hidden" name="type" id="type" value="bid"></input><br>';
				echo '<input type="submit" value="Buy"></input>';
			echo '</form>';
		echo '</div>';		
	echo "</div>";
	echo "<div class=\"asks\">";
		echo "<table class=\"asks\" id=\"orderasks\">";
			echo "<thead class=\"stripe-r\">";
				echo "<tr colspan=\"2\">";
					echo "<th class=\"asks\" >";
						echo "Asks";
					echo "</th>";	
				echo "</tr>";	
				echo "<tr>";
					echo "<th class=\"asks\" >";
						echo "Price";
					echo "</th>";
					echo "<th class=\"asks\" >";
						echo "Volume";
					echo "</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach($book['asks'] as $key=>$value) {
					echo "<tr>";	
						echo "<td class=\"asks\" id=\"orderasks\">";
							/* Load the Orderbook prices */		
							print_r($book['asks'][$key]['0']);
						echo "</td>";
						echo "<td class=\"asks\" id=\"volume\">";
							/* Load the Orderbook Volumes */		
							print_r($book['asks'][$key]['1']);	
						echo "</td>";
					echo "</tr>";			
			}
			echo "</tbody>";
		echo "</table>";
		echo '<div class="orderask">';
		echo '<br>';
			echo '<form action='.$PHP[self].'>';
					echo 'Amount: <input type="text" name="amount" id="amountbtc" class="amountbtc" value="0.00"></input><br>';
					echo 'Price: <input type="text" name="price" id="askprice" value='.$btcask.'></input><br>';
					echo '<input type="hidden" name="type" id="type" value="ask"></input><br>';
				echo '<input type="submit" value="Sell"></input>';
			echo '</form>';
		echo '</div>';	
	echo "</div>";	
echo "</div>";

 /* Unload the API */
unset($cleverAPI);
?>
</body>
</html>
