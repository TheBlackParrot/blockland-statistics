<?php
	define("CLASS_PATH", dirname(__FILE__) . '/pchart/class');
	define("FONT_PATH", dirname(__FILE__) . '/fonts');

	date_default_timezone_set("America/Chicago");

	include(CLASS_PATH . '/pData.class.php');
	include(CLASS_PATH . '/pDraw.class.php');
	include(CLASS_PATH . '/pImage.class.php');

	switch(htmlspecialchars($_GET['type'])) {
		case 'players':
			$var_sql = "PLAYER_COUNT";
			$graph['title'] = "Player Count";
			$graph['color'] = array("R"=>45,"G"=>160,"B"=>255,"Alpha"=>70);
			break;

		case 'servers':
			$var_sql = "SERVER_COUNT";
			$graph['title'] = "Server Count";
			$graph['color'] = array("R"=>10,"G"=>150,"B"=>50,"Alpha"=>70);
			break;
		
		default:
			die("Invalid graph request");
			break;
	}

	$db_filename = dirname(__FILE__) . '/SYSTEM/db/test.db';

	$db = new SQLite3($db_filename);
	if(!$db) {
		die("Error in opening the SQLite database.");
	}

	if(isset($_GET['detail'])) {
		$detail = htmlspecialchars($_GET['detail']);
		if($detail < 1) {
			$detail = 1;
		}
		$max = $db->querySingle('SELECT COUNT(*) FROM cache');
		if($detail > $max) {
			$detail = $max;
		}
	} else {
		$detail = 3;
	}

	$debug = 0;

	$cached_data = $db->query('SELECT ' . $var_sql . ',DATE FROM cache DESC WHERE _ROWID_ % ' . $detail . ' = 0 ORDER BY DATE DESC LIMIT 25');

	$cache_amount = 0;
	while($content = $cached_data->fetchArray(SQLITE3_BOTH)) {
		if(!$content[$var_sql]) {
			$data['values'][] = VOID;
		} else {
			$data['values'][] = $content[$var_sql];
		}
		$data['dates'][] = date("n/j\r\nH:i",$content['DATE']);
		$cache_amount++;
	}

	$data['values'] = array_reverse($data['values']);
	$data['dates'] = array_reverse($data['dates']);

	$graph['values'] = new pData();
	$graph['values']->addPoints($data['values'],$graph['title']); 
	$graph['values']->setSerieWeight($graph['title'],5);
	$graph['values']->setPalette($graph['title'],$graph['color']);
	$graph['values']->setAxisName(0,$graph['title']); 

	$graph['values']->addPoints($data['dates'],"Labels"); 
	$graph['values']->setSerieDescription("Labels","Time"); 
	$graph['values']->setAbscissa("Labels");

	$graph_image['values'] = new pImage(1200,370,$graph['values']); 
	$graph_image['values']->Antialias = TRUE; 
	$graph_image['values']->drawRectangle(0,0,1199,369,array("R"=>0,"G"=>0,"B"=>0)); 

	$graph_image['values']->setFontProperties(array("FontName"=>"fonts/Roboto-Medium.ttf","FontSize"=>7));
	$graph_image['values']->setGraphArea(40,10,1170,340); 
	$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>FALSE);
	$graph_image['values']->drawScale($scaleSettings); 

	//$graph_image['values']->drawLegend(540,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL)); 

	$graph_image['values']->Antialias = TRUE;
	$graph_image['values']->drawAreaChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO));
	$graph_image['values']->autoOutput(); 
?>