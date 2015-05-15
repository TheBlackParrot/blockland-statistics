<html>

<head>
	<title>Blockland Master Server Statistics</title>
	<link rel="stylesheet" type="text/css" href="css/reset.css"/>
	<style>
		body {
			background-color: #e7e7e7;
			font-family: "Roboto";
		}
		.wrapper {
			width: 1200px;
			padding: 32px;
			margin: auto;
			background-color: #fff;
			box-shadow: 0px 0px 5px rgba(0,0,0,0.33);
		}
		.graph {
			margin-bottom: 32px;
			box-shadow: 0px 3px 8px rgba(0,0,0,0.5);
		}
		h1 {
			margin-bottom: 16px;
			font-weight: 700;
			font-size: 20pt;
		}
	</style>
</head>

<body>
	<?php
		if(isset($_GET['detail'])) {
			$detail = htmlspecialchars($_GET['detail']);
		} else {
			$detail = 3;
		}
	?>
	<div class="wrapper">
		<h1>Total Players</h1>
		<img class="graph" src="graph.php?type=players&detail=<?php echo $detail; ?>"/><br/>
		<h1>Total Servers</h1>
		<img class="graph" src="graph.php?type=servers&detail=<?php echo $detail; ?>"/><br/>
	</div>
</body>

</html>