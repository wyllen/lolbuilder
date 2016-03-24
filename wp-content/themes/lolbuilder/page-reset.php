<?php

global $wpdb;


$wpdb->query( 
		"DELETE pm FROM $wpdb->postmeta pm
		INNER JOIN $wpdb->posts p
		ON p.ID = pm.post_id
		WHERE p.post_type = 'champion'"	     
);
echo 'champions supprimés <br>';

$wpdb->query( 
		"DELETE FROM $wpdb->posts
		 WHERE post_type = 'champion'"
);
echo 'champions Stats supprimés <br>';

