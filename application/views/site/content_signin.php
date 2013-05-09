<div id="register">
	<?php
	echo validation_errors();
	
	?>
	<form class="regform" enctype="multipart/form-data" action="<?=base_url()?>index.php/site/register" method="post">
		<div class="clearfix space">
			<label class="reglabel">Username:</label>
			<input type="text" class="reguser" name="reguser" value="<?=$username?>" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Profile image:</label>
			<input type="file" class="regimg" name="userfile" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Email:</label>
			<input type="text" class="regemail" name="regemail" value="<?=$email?>" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Password:</label>
			<input type="password"	class="regpass" name="regpass" value="123" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Retype Password:</label>
			<input type="password" class="regrepass" name="regrepass" value="123" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">About you:</label>
			<textarea class="regbio" placeholder="Tell us a little about you..."  name="regbio"><?=$aboutme?></textarea>
		</div>
		<div class="clearfix space">
			<label class="reglabel">Location:</label>
			<input type="text" class="reglocat" name="reglocat" value="<?=$location?>" />
		</div>

		<div class="clearfix space">
			<input type="submit" class="regsubmit regbtn" value="Submit" />
		</div>
	</form>
</div><!-- register div closed -->

<div id="signinform">
	<form class="siform" action="<?=base_url()?>/index.php/site/login" method="post">
		<div class="clearfix space">
			<label class="silabel">Email:</label>
			<input type="text" class="siemail" name="siemail" />
		</div>
		<div class="clearfix space">
			<label class="silabel">Password:</label>
			<input type="password" class="sipass" name="sipass" />
		</div>
		<input type="submit" class="sisubmit space" value="Submit">
	</form>
</div>
