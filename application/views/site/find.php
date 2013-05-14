<section class="eventfind">
	
	<div class="searchbox">
		<form id="citysearch"action="<?=base_url()?>/index.php/site/search" method="post">
			<input type="text" name="citysearch" id="usersearch" placeholder="Search Events By City">
			<input type="submit" id="searchsubmit" value="Find">
		</form>
	</div>
	<div class="results">
		<?
			if($events){
			foreach($events as $event){
			
			
		?>
		<div class="result">
			<ul class="resultitem">
				<li class="rdate"><?= date("m.d.y",strtotime($event->date))?></li>
				<li class="rtime"><?=date("g:i A",strtotime($event->date))?></li>
				<li class="rimage"><img width="90" src="<?=base_url().'/uploads/'.$event->thumb?>" /></li>
				<li class="ruser"><span class="rlabel">Host:</span><?=$event->creator?></li>
				<li class="rcity"><span class="rlabel">City: </span><?=$event->city?></li>
				<li class="revent"><?=$event->name?></li>
				<li class="rdesc"><?=$event->description?></li>
				<li class="rattend"><?=$event->name?></li>
			</ul>
		</div>
		<?
		}
	}else{
		echo 'No Events in this City';
	}
		?>
	</div>
</section>

