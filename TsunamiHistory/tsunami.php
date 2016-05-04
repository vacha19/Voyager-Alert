<?php
	$file = file('tsrunup.txt');

	$mysql = new mysqli("localhost", "cs125", "cs125", "cs125");
	$mysql->set_charset("utf8");
	$mysql->query("DELETE FROM cs125.history WHERE `type`=1");

	$add = $mysql->prepare('INSERT INTO `cs125`.`history` (`type`, `geo_lat`, `geo_long`, `deaths`, `time`, `details`) VALUES (1, ?, ?, ?, ?, ?)');
	echo $mysql->error;
	$geo_lat=0;
	$geo_long=0;
	$deaths = 0;
	$time = 0;
	$details = '';
	$add->bind_param('ddiis', $geo_lat, $geo_long,$deaths,$time,$details);

	foreach( $file as $line ) {
		list ($I_D,$TSEVENT_ID,$YEAR,$MONTH,$DAY,$DOUBTFUL,$COUNTRY,$STATE,$LOCATION_NAME,$LATITUDE,$LONGITUDE,$REGION_CODE,$DISTANCE_FROM_SOURCE,$TRAVEL_TIME_HOURS,$TRAVEL_TIME_MINUTES,$WATER_HT,$HORIZONTAL_INUNDATION,$TYPE_MEASUREMENT_ID,$PERIOD,$FIRST_MOTION,$DEATHS,$DEATHS_DESCRIPTION,$INJURIES,$INJURIES_DESCRIPTION,$DAMAGE_MILLIONS_DOLLARS,$DAMAGE_DESCRIPTION,$HOUSES_DAMAGED,$HOUSES_DAMAGED_DESCRIPTION,$HOUSES_DESTROYED,$HOUSES_DESTROYED_DESCRIPTION) = explode("\t", $line);

		if( $YEAR > 1970 && $YEAR < 2020 && $DEATHS > 1 ) {
			echo "$LOCATION_NAME,$LATITUDE,$LONGITUDE\n";
			$geo_lat = $LATITUDE;
			$geo_long = $LONGITUDE;

			$deaths = $DEATHS;

			$time = strtotime( "$YEAR-$MONTH-$DAY" );
			$details = "Tsunami Runup: $COUNTRY,$STATE,$LOCATION_NAME \n\n$line";
			$add->execute();
		}

	}

?>
