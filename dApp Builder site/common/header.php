<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>dApp Builder Prototype</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/profile.css" rel="stylesheet">
	<link href="assets/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<!--<script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=5991703cb25a6f00127f1f6f&product=inline-share-buttons' async='async'></script>-->
	
	<!-- Facebook Pixel Code -->
	<script>
	  !function(f,b,e,v,n,t,s)
	  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
	  n.queue=[];t=b.createElement(e);t.async=!0;
	  t.src=v;s=b.getElementsByTagName(e)[0];
	  s.parentNode.insertBefore(t,s)}(window, document,'script',
	  'https://connect.facebook.net/en_US/fbevents.js');
	  fbq('init', '1719504321686385');
	  fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
	  src="https://www.facebook.com/tr?id=1719504321686385&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Facebook Pixel Code -->
	
	<script type="text/javascript" src="assets/js/jquery.min.js"></script>
</head>
<body>
    <div id="wrapper">
	
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- <a class="navbar-brand" href="/builder/">dApp <span class="color">Builder</span></a> -->
                <a class="navbar-brand" href="/builder/"><img src="../images/logo.png" alt="dApp"></a>
            </div>
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <span id="username"><?=$username?></span> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
						<li>
                            <a href="#" data-toggle="modal" data-target="#usernameModal"><i class="fa fa-fw fa-refresh"></i> Change Username</a>
                        </li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-fw fa-refresh"></i> Change Password</a>
                        </li>
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
			
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
					<li id="my-dapps-li" <?php if (!$deployed_dapps) { ?>style="display:none;"<?php } ?> <?php if (preg_match('/^\/builder\/my-dapps\.php/', $_SERVER['REQUEST_URI'])) { ?>class="active"<?php } ?>>
						<a href="my-dapps.php"><i class="fa fa-fw fa-cloud"></i> My dApps</a>
					</li>
					<li <?php if (preg_match('/^\/builder\/new-dapp\.php/', $_SERVER['REQUEST_URI'])) { ?>class="active"<?php } ?>>
                        <a href="new-dapp.php"><i class="fa fa-fw fa-cloud"></i> Create New dApp</a>
                    </li>
					<?php if ($application && $added_dapps) { ?>
						<li <?php if (preg_match('/^\/builder\/mobile-app\.php/', $_SERVER['REQUEST_URI'])) { ?>class="active"<?php } ?>>
							<a href="mobile-app.php"><i class="fa fa-fw fa-dashboard"></i> My Mobile App</a>
						</li>
					<?php } ?>
					<li>
                        <a href="https://github.com/iBuildApp/dApp-Builder" target="_blank"><i class="fa fa-fw fa-github"></i> Our GitHub Repo</a>
                    </li>
					<li>
                        <a href="https://www.rinkeby.io/" target="_blank"><i class="fa fa-fw fa-globe"></i> Rinkeby Test Net</a>
                    </li>
                </ul>
            </div>
			
        </nav>