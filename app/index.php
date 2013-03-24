<?php

if(isset($_POST['setup']))
{
    $output = shell_exec('sudo mkdir /etc/raspcontrol');
    
    // Prepare the oven
    shell_exec('sudo chown root:root /etc/raspcontrol');
    shell_exec('sudo chmod 0777 /etc/raspcontrol');
    shell_exec('sudo touch /etc/raspcontrol/database.aptmnt');
    shell_exec('sudo chown root:root /etc/raspcontrol/database.aptmnt');
    shell_exec('sudo chmod 0777 /etc/raspcontrol/database.aptmnt');
    
    // Open the door
    $myFile = "/etc/raspcontrol/database.aptmnt";
    if(!file_exists($myFile))
    {
        require('_lib/includes/_header.php');
?>
        <div id="firstBlockContainer">
          <div class="firstBlockWrapper">
            <div style="padding-top: 20px;">
                The attempt to create the config file failed, so please open a Terminal session and run the commands below:<br/>
                <br/> 
                sudo mkdir /etc/raspcontrol<br/>
                sudo nano /etc/raspcontrol/database.aptmnt<br/>
                <br/>
                Once you are in the editor, add the lines below and press the keys "CTRL+X", "Y" and "ENTER"<br/>
                {<br/>
                  "user":"guest",<br/>
                  "password":"guest"<br/>
                }<br/>
            </div>
          </div>
          <br/><br/><br/>
        </div>
<?php
        require('_lib/includes/_footer.php'); 
        die();
    }
    else
    {
        $fh = fopen($myFile, 'w');
    }

    // Bake that Pi
    $stringData = '{
        "user":		"' . $_POST['username'] .'", 
        "password":	"' . $_POST['password'] .'"
    }';
    fwrite($fh, $stringData); 

    // Eat it
    header('location: index.php');

    }
    else
    {
        $filename = '/etc/raspcontrol/database.aptmnt';

        if (file_exists($filename))
        {
            session_start();

            if($_SESSION['username'] != "")
            {
                require('main.php'); 
                die;
            }

            require('_lib/includes/_header.php');
            require('_lib/classes/_login.php'); 
?>

            <div id="firstBlockContainer">
              <div class="firstBlockWrapper">
                <div style="padding-top: 20px;">
                  <center>
                    Please login to Raspcontrol!<br/><br/>
                    <form name="login" method="post" action="index.php">
                      <input type="text" name="username" class="loginForm"
                        onfocus="if (this.value == 'Username') {this.value = '';}"
                        onblur="if (this.value == '') {this.value = 'Username';}" value="Username">
                      <input type="password" name="password" class="loginForm"
                        onfocus="if(this.value == 'Password') {this.value = '';}"
                        onblur="if (this.value == '') {this.value = 'Password';}" value="Password"><br/>
                      <input type="submit" value="Login" name="login" class="minimal">
                      <br/><br/>
<?php
                      if($wrong == 1)
                      {
                          echo "<font color='red'>Incorrect Username/Password</font>";
                      }
?>
                    </form>
                    <br/><br/>
                    Raspcontrol has been drastically improved! &middot <a href="https://github.com/Bioshox/Raspcontrol/blob/master/README.md" target="_blank">Find out more</a> 
                  </center>
       	        </div>
              </div>
              <br/><br/><br/>
	    </div>
<?php    
	}
        else
        {
            require('_lib/includes/_header.php');
?>

            <div id="firstBlockContainer">
              <div class="firstBlockWrapper">
                <strong>Raspcontrol Installation</strong>
                <br/><br/>	
                <center>
                    Please choose a username and password to login with<br/><br/>
                    <form name="setup" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                      <input type="text" name="username" class="loginForm" 
                        onfocus="if(this.value == 'Username') {this.value = '';}"
                        onblur="if (this.value == '') {this.value = 'Username';}" value="Username">
                      <input type="password" name="password" class="loginForm"
                        onfocus="if(this.value == 'Password') {this.value = '';}"
                        onblur="if (this.value == '') {this.value = 'Password';}" value="Password"><br/>
                      <input type="submit" value="Create Account" name="setup" class="minimal">
                </center>
                <br/><br/><br/><br/>
                </form>
              </div>
            </div>
<?php
        }
}

require('_lib/includes/_footer.php'); 
