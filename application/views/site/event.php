<div id="createform">
	<?php
	echo validation_errors();
	?>
	<form class="regform" enctype="multipart/form-data" action="<?=base_url()?>index.php/site/create" method="post">
		<div class="clearfix space">
			<label class="reglabel">Event Name:</label>
			<input type="text" class="reguser" name="ename" value="" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Event image:</label>
			<input type="file" class="eimg" name="userfile" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Time:</label>
<!-- 			<input type="time" class="etime" name="etime" value="" /> -->
			<select name="etime">
				<?php
					foreach($default_times as $dt) {
						echo '<option value="' . $dt . '">' . $dt . '</option>';
					}
				?>
			</select>
		</div>
		<div class="clearfix space">
			<label class="reglabel">Date:</label>
			<input type="date" id="datepicker" class="edate" name="edate" value="" />
		</div>
		<div class="clearfix space">
			<label class="reglabel">Event Description:</label>
			<textarea class="regbio" placeholder="Tell us about the event..." name="edesc"></textarea>
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