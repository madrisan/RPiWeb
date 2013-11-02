<?php
session_start();

if($_SESSION['username'] == "")
{
    die("You are not logged in");
}

?>

<html>

<head>
<style type="text/css">
body{
    font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
    font-size:12px;
}
p, h1, form, button{border:0; margin:0; padding:0;}
.spacer{clear:both; height:1px;}
/* ----------- My Form ----------- */
.myform{
    margin:0 auto;
    width:400px;
    padding:14px;
}

/* ----------- stylized ----------- */
#stylized{
    border:solid 2px #666666;
    background:#D9E0DB;
}
#stylized h1 {
    font-size:14px;
    font-weight:bold;
    margin-bottom:8px;
}
#stylized p{
    font-size:11px;
    color:#666666;
    margin-bottom:20px;
    border-bottom:solid 1px #666666;
    padding-bottom:10px;
}
#stylized label{
    display:block;
    font-weight:bold;
    text-align:right;
    width:140px;
    float:left;
}
#stylized .small{
    color:#666666;
    display:block;
    font-size:11px;
    font-weight:normal;
    text-align:right;
    width:140px;
}
#stylized input{
    float:left;
    font-size:12px;
    padding:4px 2px;
    border:solid 1px #666666;
    width:230px;
    margin:2px 0 20px 10px;
}
#stylized select{
    float:left;
    font-size:12px;
    padding:4px 2px;
    border:solid 1px #666666;
    width:230px;
    margin:2px 0 20px 10px;
}
#stylized button{
    clear:both;
    margin-left:150px;
    width:125px;
    height:31px;
    background:#666666;
    text-align:center;
    line-height:31px;
    color:#FFFFFF;
    font-size:11px;
    font-weight:bold;
}
</style>
</head>

<body>
<div id="stylized" class="myform">
<form id="form" name="wifiSettings" method="post" action="_configurewifi.php">
<h1>Wireless Interface Configuration</h1>
<p>Set the wireless SSID and the WEP Key or WPA Passphrase</p>

<label>SSID
<span class="small">Wireless SSID</span>
</label>
<select name="ssid">
<?php
exec('sudo /usr/sbin/iwlist wlan0 scanning | \
        sed -n "/ESSID:/{s,.*ESSID:\"\(.*\)\",\1,p}"',
    $essid_list, $retval);
if (empty($essid_list))
{
    print "<option value='none_detected'>No Wireless Networks...</option>";
}
else
{
    foreach ($essid_list as $essid)
       print "<option value="."\"$essid\"".">$essid</option>";
}
?>
</select>

<label>Encryption
<span class="small">Encryption Technology</span>
</label>
<select name="encryption">
  <option value="wep">WEP</option>
  <option selected="selected" value="wpa">WPA/WPA2</option>
  <option value="none">None</option>
</select>

<label>Secret
<span class="small">WEP Encryption Key WPA passphrase</span>
</label>
<input type="text" name="secret"/>

<button name="configure_wireless" type="submit">Configure</button>
<div class="spacer"></div>

<?php
if(isset($_POST['configure_wireless']))
{
    if (($_POST['encryption'] != 'none' &&
         $_POST["ssid"] != "none_detected" && $_POST['secret'] != "") ||
        ($_POST['encryption'] == 'none' && $_POST["ssid"] != "none_detected"))
    {
        system('sudo /usr/sbin/config-rpi-wireless' .
               ' --force --check-result --interface-alias=wireless' .
               ' --encryption="' . $_POST['encryption'] . '"' .
               ' --ssid="' . $_POST["ssid"] . '"' .
               ' --secret="' . $_POST['secret'] . '"', $retval);
        if ($retval == 0)
        {
            echo "<font color='black'>OK</font>";
            sleep(3);
?>
            <script type="text/javascript">
            <!--
            window.location = "../../main.php"
            //-->
            </script>
<?php
        }
        else
        {
            echo "<font color='red'>Configuration error</font>";
        }
    }
    else
    {
        echo "<font color='red'>Input error</font>";
    }
}
?>

</form>
</div>

</body>
</html>
