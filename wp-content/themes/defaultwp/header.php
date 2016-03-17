<!DOCTYPE html>
<!--[if lte IE 8 ]> <html class="no-js lte-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js" <?php language_attributes(); ?>> 
<!--<![endif]-->
	<title><?php wp_title(); ?></title>	
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon.ico" />
	<?php wp_head(); ?>
</head>
<body <?php echo body_class(); ?> role="document">
	<header class="header" role="banner">
	</header>
	<section class="content-wrapper">