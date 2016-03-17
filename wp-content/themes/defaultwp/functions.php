<?php

/**
-------- Chargement des fichiers de fonctions------------
**/
	if(is_admin())
	// POUR LE BACK OFFICE
		get_template_part( 'functions', 'adminonly' );
	else
	// POUR LE FRONT OFFICE
		get_template_part( 'functions', 'frontendonly' );


/**
-------- Menus ------------
**/

register_nav_menus( array(
    'main_menu' => __( 'Menu principal', 'oab' )
) );


/**
-------- Post thumbnail support ------------
**/
add_theme_support( 'post-thumbnails' );

/**
-------- Get the slug ------------
**/

function get_the_slug( $id=null ){
  if( empty($id) ):
    global $post;
    if( empty($post) )
      return ''; // No global $post var available.
    $id = $post->ID;
  endif;

  $slug = basename( get_permalink($id) );
  return $slug;
}
