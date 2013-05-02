<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="<?= base_url()?>css/web.css" media="screen" />
		<title>Out.Spoken</title>
	</head>
	<body>
		<div id="wrapper">
			<header>
				<div id="branding"></div> <!-- Branding div close -->
				<div class="nav clearfix">
					
					<div id="findbox" class="navitem">
						<a href="<?=base_url()?>/index.php/site/find" id="find" class="ab">Find
							<span class="subdesc">Something to do...</span></a>
					</div>
					
					<div id="createbox" class="navitem">
						<a href="#" id="create" class="ab">Create<br/>
							<span class="subdesc2">a goodtime.</span></a>
					</div>
					<?
						if($loggedin){?>
							<a href="<?= base_url()?>index.php/site/logout" id="signin" class="ab navitem ">Sign Out</a>
					<? }else{?>
						<a href="<?= base_url()?>index.php/site/register" id="signin" class="ab navitem ">Sign In</a>
					<? }
						
					?>
					
					
				</div> <!-- Nav div closed -->
			</header>
