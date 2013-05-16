<?php
	echo validation_errors();

?>
	<div class="cta clear">
				<h1 class="ctahead"><span class="ats">Go Out</span> Ride Hard</h1>
				<p class="symone">O</p>
				<p class="symtwo">E</p>
				<p class="symthr">N</p>
				<div class="ctasub"><h2>Create Events. <span class="subsec">Meet New People.</span> <span class="subthi">Keep in Touch.</span></h2></div>
				<div class="ctaleft"></div>
			 	<div class="ctaright"></div>
			</div> <!-- CTA div closed -->
			
			<section class="content">
				<h2 class="eventhead">Top Events</h2>
				<ul class="topevents clearfix">
				<?					
				foreach($events as $e){
					?>
					<li class="eventitem">
						<div class="event_img"><img src="<?=base_url().'uploads/'.$e->thumb?>" /></div>
						<div class="eventdate_wrapper"><p class="eventdate"><?=date('m/d/Y', strtotime($e->date))?><br><a href="#" class="eventname"><?=$e->name?></a></p></div>
						<div class="clear"></div>
					</li> <!-- Eventitem Close -->
				<? }?>
				</ul> <!-- Events div closed -->
				
				<div class="clear"></div>
				
					
						
					
			</section>
