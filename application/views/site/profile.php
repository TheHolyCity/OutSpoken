<section class="profileview">
	<div class="proleftcol">
		<div class="imgarea">
			<a href="#"<div id="profileimg"></div></a>
		</div>
		<div id="userdetails">
			<a href="#"><div class="edituserbtn"><span>Edit Profile</span></div></a>
			<p id="prouser"><?=$username?></p>
			<p id="procity"><?=$location?></p>
			
		</div>
		<div id="proabout">
			<p id="aboutme"><?=$aboutme?></p>
		</div>
	</div>
	<div class="promidcol">
		<div class="procreatedevents">
			<ul>
			<? 
				if($events)
				{
					foreach($events as $ue)
					{
						echo("<li class='puserevent'><a></a></li>");
					}
				}else
				{
					echo("<li class='puserevent'>This user has no active Events</li>");
				}
			?>
			</ul>
		</div>
	</div>
	<div class="prorightcol">
		<form action="<?=base_url()?>index.php/site/galleryupload" enctype="multipart/form-data" method="post" id="galleryupload">
			<input type="file" id="galleryup" name="userfile">
			<input type="submit" id="gallerysubmit">
		</form>
		<div class="imggallery">
		<?
			if($galleryimgs)
			{
				foreach($galleryimgs as $img)
				{
					
					echo('<div class="galimg"> <img src="'.base_url().'/uploads/'.$img->thumb.'">  </div>');
				}
			}
		?>
		</div>	
	</div>
</section>