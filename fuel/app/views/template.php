<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
		<title><?php echo $title; ?></title>
		<meta name="description" content=""/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<?php echo Casset::render_css(); ?>
	</head>
	<body>
		<div class="container-fluid" style="padding: 0;">
			<div class="row-fluid">
				<div class="span12">
					<?php echo $content; ?>
				</div>
			</div>
		</div>
		
		<?php echo Casset::render_js(); ?>
	</body>
</html>
