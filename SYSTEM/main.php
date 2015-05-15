<?php
	// this is meant to be used with a cron job
	
	$db_filename = dirname(__FILE__) . '/db/test.db';
	$db_nf = 0;

	if(!is_file($db_filename)) {
		$db_nf = 1;
	}
	$db = new SQLite3($db_filename);
	if(!$db) {
		die("Error in opening the SQLite database.");
	}

	if($db_nf) {
		$query = 'CREATE TABLE cache (ID INT PRIMARY KEY, DATE TEXT DEFAULT "N/A", PLAYER_COUNT INT DEFAULT 0, SERVER_COUNT INT DEFAULT 0, PASSWORDED INT DEFAULT 0, DEDICATED INT DEFAULT 0, GAMEMODES TEXT DEFAULT NULL)';
		$db->exec($query);
	}

	$list_filen = "http://master2.blockland.us";
	$server_list = fopen($list_filen, "r");

	$debug = 1;

	$i = 0;
	$skipped_first_line = 0;
	$passworded = 0;
	$dedicated = 0;
	$players = 0;
	$gamemodes = [];

	if($server_list) {
		while(($buffer = fgets($server_list)) !== FALSE) {
			if($skipped_first_line < 2) {
				// skip the first and second line, declares column names
				$skipped_first_line++;
				continue;
			}

			$buffer = rtrim($buffer);

			if($buffer) {
				if($buffer == "END") {
					continue;
				}
			} else {
				continue;
			}

			$fields = explode("\t",$buffer);
			foreach ($fields as $tmp) {
				$tmp = addslashes($tmp);
			}

			$data[$i]['playercount'] = $fields[5];
			$players += $fields[5];
			$data[$i]['brickcount'] = $fields[8];

			if($fields[2]) {
				$passworded++;
			}
			if($fields[3]) {
				$dedicated++;
			}

			if($fields[7]) {
				if(!in_array($fields[7],$gamemodes)) {
					$gamemodes[] = $fields[7];
				}
			}

			foreach ($fields as $tmp) {
				$tmp = stripslashes($tmp);
			}

			$i++;
		}

		$query = 'INSERT INTO cache (DATE,PLAYER_COUNT,SERVER_COUNT,PASSWORDED,DEDICATED,GAMEMODES) VALUES (\'' . SQLite3::escapeString(rtrim(time())) . "','" . SQLite3::escapeString(rtrim($players)) . "','" . SQLite3::escapeString(rtrim($i)) . "','" . SQLite3::escapeString(rtrim($passworded)) . "','" . SQLite3::escapeString(rtrim($dedicated)) . "','" . SQLite3::escapeString(rtrim(implode(",,",$gamemodes))) . "')";
		$db->exec($query);

		if($debug) {
			echo "<strong>Player Count:</strong> $players<br/><br/>";

			echo "<strong>Server Count:</strong> $i<br/>";
			echo "<strong>Dedicated Servers:</strong> $dedicated<br/>";
			echo "<strong>Passworded Servers:</strong> $passworded<br/>";

			echo "<strong>Detected Gamemodes:</strong>";
			echo "<pre>";
			var_dump($gamemodes,true);
			echo "</pre>";
		}
	} else {
		die("Could not open file.");
	}

	fclose($server_list);
?>