<?php
session_start();

if($_SESSION['username'] == "")
{
    die("You are not logged in");
}

echo '<pre>';

system('sudo /usr/bin/smart update', $retval);
$last_line = system('sudo /usr/bin/smart --quiet upgrade --yes', $retval);

// Printing additional info
echo '</pre>';
?>

<?php
if ($retval == 0)
{
?>
The RPi has been succefully updated!<br/>
<?php
}
else
{
?>
<font color='red'>The RPi update failed!</font><br/>
<?php
}
?>

<br/>
<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Return To Previous Page</a>
