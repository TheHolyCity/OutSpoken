<div id="createform">
	<?php
	echo validation_errors();
	
	?>
	<form class="regform" action="<?=base_url()?>index.php/site/create" method="post">
		<div class="clearfix space">
			<label class="reglabel">Event Name:</label>
			<input type="text" class="reguser" name="ename" value="" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Event image:</label>
			<input type="file" class="eimg" name="regimg" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Time:</label>
			<input type="time" class="etime" name="regemail" value="" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Date:</label>
			<input type="date"	class="eDate" name="regpass" value="" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Event Description:</label>
			<textarea class="regbio" placeholder="Tell us about the event..."  name="edisc"></textarea>
		</div>
		<div class="clearfix space">
			<label class="reglabel">Location:</label>
			<input type="text" class="reglocat" name="elocat" value="" />
		</div>

		<div class="clearfix space">
			<input type="submit" class="regsubmit regbtn" value="Submit" />
		</div>
	</form>
</div><!-- register div closed -->