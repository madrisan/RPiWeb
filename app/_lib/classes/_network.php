<?php
/*
function networkUsage() {
    $string = exec("/sbin/ifconfig eth0 | grep RX\ bytes", $out);
    $string = str_ireplace("RX bytes:", "", $string);
    $string = str_ireplace("TX bytes:", "", $string);
    $string = trim($string);
    $string = explode(" ", $string);
    $out['rx'] = ($string[0] / 1024 / 1024);
    $out['rx'] = round($out['rx'], 2);
    $out['tx'] = ($string[4] / 1024 / 1024);
    $out['tx'] = round($out['tx'], 2);
    return $out;
}
 * */

class network{
	function networkUsage($statsOnly = 0) {
	
	$netType = shell_exec("/sbin/ifconfig");
	$netTypeRaw = explode(" ", $netType); 
	$netTypeFormatted = str_replace("encap:", "", $netTypeRaw);
	
	
    $dataThroughput = exec("/sbin/ifconfig wlan0 | grep RX\ bytes", $out);
    $dataThroughput = str_ireplace("RX bytes:", "", $dataThroughput);
    $dataThroughput = str_ireplace("TX bytes:", "", $dataThroughput);
    $dataThroughput = trim($dataThroughput);
    $dataThroughput = explode(" ", $dataThroughput);
    
    
    
    $rxRaw = $dataThroughput[0] / 1024 / 1024;
    $txRaw = $dataThroughput[4] / 1024 / 1024;
	$rx = round($rxRaw, 2)." ";
	$tx = round($txRaw, 2);
	$totalRxTx = $rx + $tx;

		$iTotalConnections = shell_exec("netstat -nta --inet | wc -l");
		$iTotalConnections--;
		
	if ($statsOnly) {
		echo '"'.$netTypeFormatted[7].'" : {
			"received" : "'.$rx.'MB",
			"sent" : "'.$tx.'MB",
			"total" : "'.$totalRxTx.'MB",
			"active" : "'.substr($iTotalConnections, 0, -1).'"
		}';
		return;
	}
	?>

	
	
	<div class="networkIcon">
		  	<img src='_lib/images/network.png' align='middle'>
		  </div> 
		  
		  <div class="networkTitle">
		  	Network
		  </div>
		  
		  <div class="networkText">
			<strong> <?php echo $netTypeFormatted[7]; ?> | </strong> Received: <strong><?php echo $rx; ?> MB</strong> &middot Sent: <strong><?php echo $tx; ?> MB</strong> &middot Total: <strong><?php echo $totalRxTx; ?> MB</strong> <br /> Active Network Connections: <strong><?php echo $iTotalConnections; ?></strong>
		  </div>
	
	
	
<?php
}
}

