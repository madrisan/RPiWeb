<?php

class network
{
    function networkUsage($statsOnly = 0)
    {
        $defaultroute = shell_exec("/bin/netstat -r | grep '^default'");
        $keywords = preg_split("/[\s,]+/", "$defaultroute");
        $iface = $keywords[7];

        $netType = shell_exec("/sbin/ifconfig " . $iface);
        $netTypeRaw = explode(" ", $netType); 
        $netTypeFormatted = str_replace("encap:", "", $netTypeRaw);

        $dataThroughput = exec("/sbin/ifconfig " . $iface .
                                   " | grep RX\ bytes", $out);
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

        if ($statsOnly)
        {
            echo '"' . $netTypeFormatted[7] ($iface). '" : {
                "received" : "'.$rx.'MB",
                "sent" : "'.$tx.'MB",
                "total" : "'.$totalRxTx.'MB",
                "active" : "'.substr($iTotalConnections, 0, -1).'"
            }';
        }
?>
        <div class="networkIcon">
          <img src='_lib/images/network.png' align='middle'>
        </div>
        <div class="networkTitle">Network</div>

        <div class="networkText">
          <strong><?php echo "$netTypeFormatted[7] ($iface)";?> | </strong>
            Received: <strong><?php echo $rx;?> MB</strong> &middot
            Sent: <strong><?php echo $tx; ?> MB</strong> &middot
            Total: <strong><?php echo $totalRxTx; ?> MB</strong><br/>

            Active Network Connections: <strong><?php echo $iTotalConnections; ?>
          </strong>
        </div>
<?php
    }
}
?>
