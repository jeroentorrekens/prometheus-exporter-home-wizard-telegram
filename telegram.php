<?php

function parse_telegram($telegram) {

	$values = array(
			"96.1.4"=> array("name"=> "version", "unit"=> "String", "value"=> "", "type"=> "untyped", "help" => "Version of software on meter"),
			"96.1.1"=> array("name"=> "serial_number", "unit"=> "String", "value"=> "", "type"=> "untyped", "help" => "Serial number of meter"),
			"1.0.0"=> array("name"=> "current_date", "unit"=> "Date YYMMDDHHMMSS W=winter time, S=summer time", "value"=> "", "type"=> "untyped", "help" => "Current date on the meter"),
			"1.8.0"=> array("name"=> "power_import_total", "unit"=> "kWh", "value"=> 0, "type"=> "counter", "help" => "Imported/consumed power day+night"),
			"1.8.1"=> array("name"=> "power_import_day", "unit"=> "kWh", "value"=> 0, "type"=> "counter", "help" => "Imported/consumed power during the day"),
			"1.8.2"=> array("name"=> "power_import_night", "unit"=> "kWh", "value"=> 0, "type"=> "counter", "help" => "Imported/consumed power during the night/weekend"),
			"2.8.0"=> array("name"=> "power_export_total", "unit"=> "kWh", "value"=> 0, "type"=> "counter", "help" => "Exported power day+night"),
			"2.8.1"=> array("name"=> "power_export_day", "unit"=> "kWh", "value"=> 0, "type"=> "counter", "help" => "Exported power during the day"),
			"2.8.2"=> array("name"=> "power_export_night", "unit"=> "kWh", "value"=> 0, "type"=> "counter", "help" => "Exporter power during the night/weekend"),
			"96.14.0"=> array("name"=> "tarrif_schedule", "unit"=> "1=day, 2=night", "value"=> 0, "type"=> "gauge", "help" => "Current active tarrif 1=day, 2=night"),
			"1.7.0"=> array("name"=> "power_import_current", "unit"=> "kW", "value"=> 0, "type"=> "gauge", "help" => "Current power import/consumption"),
			"2.7.0"=> array("name"=> "power_export_current", "unit"=> "kW", "value"=> 0, "type"=> "gauge", "help" => "Current power export"),
			"21.7.0"=> array("name"=> "power_l1_import_current", "unit"=> "W", "value"=> 0, "type"=> "gauge", "help" => "Current power import on L1"),
			"41.7.0"=> array("name"=> "power_l2_import_current", "unit"=> "W", "value"=> 0, "type"=> "gauge", "help" => "Current power import on L2"),
			"61.7.0"=> array("name"=> "power_l3_import_current", "unit"=> "W", "value"=> 0, "type"=> "gauge", "help" => "Current power import on L3"),
			"22.7.0"=> array("name"=> "power_l1_export_current", "unit"=> "W", "value"=> 0, "type"=> "gauge", "help" => "Current power export on L1"),
			"42.7.0"=> array("name"=> "power_l2_export_current", "unit"=> "W", "value"=> 0, "type"=> "gauge", "help" => "Current power export on L2"),
			"62.7.0"=> array("name"=> "power_l3_export_current", "unit"=> "W", "value"=> 0, "type"=> "gauge", "help" => "Current power export on L3"),
			"32.7.0"=> array("name"=> "voltage_l1_current", "unit"=> "V", "value"=> 0, "type"=> "gauge", "help" => "Voltage on L1"),
			"52.7.0"=> array("name"=> "voltage_l2_current", "unit"=> "V", "value"=> 0, "type"=> "gauge", "help" => "Voltage on L2"),
			"72.7.0"=> array("name"=> "voltage_l3_current", "unit"=> "V", "value"=> 0, "type"=> "gauge", "help" => "Voltage on L3"),
			"31.7.0"=> array("name"=> "current_l1_current", "unit"=> "A", "value"=> 0, "type"=> "gauge", "help" => "Current on L1"),
			"51.7.0"=> array("name"=> "current_l2_current", "unit"=> "A", "value"=> 0, "type"=> "gauge", "help" => "Current on L2"),
			"71.7.0"=> array("name"=> "current_l3_current", "unit"=> "A", "value"=> 0, "type"=> "gauge", "help" => "Current on L3"),
			"96.3.10"=> array("name"=> "disconnected_status", "unit"=> "0=no,1=yes,2=ready for reconnect", "value"=> "", "type"=> "gauge", "help" => "Reports if your meter is disconnected from the grid"),
			"17.0.0"=> array("name"=> "power_limiter", "unit"=> "kW", "value"=> 0, "type"=> "gauge", "help" => "Reports if there is a power limitation active. 999 = not active"),
			"31.4.0"=> array("name"=> "current_limiter", "unit"=> "A", "value"=> 0, "type"=> "gauge", "help" => "Reports if there is current limitation active. 999 = not active"),
			"96.13.0"=> array("name"=> "message_from_fluvius", "unit"=> "String", "value"=> "", "type"=> "untyped", "help" => "Future use - messages from Fluvius")
			);
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $telegram) as $line){
		preg_match('/([0-9]*-[0-9]*):([0-9]*\.[0-9]*\.[0-9]*)\(([0-9.]*)/', $line, $matches, PREG_OFFSET_CAPTURE);
		if ( count($matches) > 1 ) {
			$code = $matches[2][0];
			if ( in_array($values[$code]["unit"], ["kWh","kW","W","V","A"]) ) {
				echo "# HELP ".$values[$code]["name"]." ".$code." ".$values[$code]["help"]."\n";
				echo "# TYPE ".$values[$code]["name"]." ".$values[$code]["type"]."\n";
				echo $values[$code]["name"]." ".floatval($matches[3][0])."\n";
			} else {
				echo "# HELP ".$values[$code]["name"]." ".$code." ".$values[$code]["help"]."\n";
				echo "# TYPE ".$values[$code]["name"]." ".$values[$code]["type"]."\n";
				echo $values[$code]["name"]." ".floatval($matches[3][0])."\n";
			}
		}
	} 

}

header('Content-Type: text/plain; version=0.0.4; charset=utf-8');

# Create curl object
$ch = curl_init();
$timeout = 5;
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

# Get most recent telegram
curl_setopt($ch, CURLOPT_URL, "http://192.168.100.12/api/v1/telegram");
$telegram = curl_exec($ch);
curl_close($ch);

parse_telegram($telegram);




?>
