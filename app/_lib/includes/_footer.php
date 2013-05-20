<div id="footerContainer">
        <div class="footerWrapper">
			<?php
			if($_Username == ""){}else{
				echo 'Poll timer: <select onchange="poll.adjustDelay(this.value);">
				<option value="30000">30 seconds</option>
				<option value="60000" selected>1 minute</option>
				<option value="300000">5 minutes</option>
				<option value="600000">10 minutes</option>
			</select> <input type="button" value="Update now" onclick="poll.update(true);"/><br/>
			Last update: <span id="lastAJAXUpdate">never</span>';
			}
			?>
			<br/><br/>
            Powered by <a href="http://www.openmamba.org" target="_blank">openmamba</a>
            
            <?php
            
            if($_Username == ""){}else{
                    echo '<br/><br/><a href="_lib/classes/_logout.php">Logout of RPiWeb</a><br/><br/>';
				
            }
            ?>
        </div>
    </div>
</body>
</html>
