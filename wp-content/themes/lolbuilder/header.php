<!DOCTYPE html>
<!--[if lte IE 9 ]> <html class="no-js lt-ie9 lte-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <title><?php wp_title(); ?></title> 
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
  
    <?php wp_head(); ?>

</head>
<body <?php echo body_class(); ?> role="document">