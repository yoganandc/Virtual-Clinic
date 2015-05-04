</head>
<body>
	<div id="header">
		<div id="header-right">
			<ul id="nav">
				<li><a title="Home" href="<?php echo VC_LOCATION.'index.php'; ?>">Home</a></li>
				<li>|</li>
				<?php if($_SESSION['type'] != VC_ADMINISTRATOR) { ?>
				<li><a title="My Account" href="<?php echo VC_LOCATION.'profile.php'; ?>">My Account</a><?php echo '<span class="username"> ('.$_SESSION['username'].')</span>'; ?></li>
				<li>|</li>
				<?php } ?>
				<li><a title="Log out" href="<?php echo VC_LOCATION.'logout.php'; ?>">Log Out</a></li>
			</ul>
			<?php if($_SESSION['type'] != VC_ADMINISTRATOR) { ?>
			<form action="<?php echo VC_LOCATION.'search.php'; ?>" method="GET">
				<input type="text" id="query" name="query" value="<?php if(isset($_GET['query'])) echo $_GET['query']; ?>">
				<input type="submit" id="search">
			</form>
			<?php } ?>
		</div>
		<h1>Virtual Clinic</h1>
	</div>
	<div id="main">
