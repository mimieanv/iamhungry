<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<?php require FOLDER_BASE_SITE.'/includes/headers.php'; ?>
        <title>"I am hungry..."  "So bad for you dude!"</title>
	</head>
	
	<body>
		<?php
			$header = SHOPUTT::getInstance()->loadModule('SetInhand');
			$header->display();
		?>
	</body>

</html>
