<?php
function update_meta_value($column, $value, $postID){
	global $wpdb;
	$wpdb->replace( 
	'lolb_postmeta', 
	array(
		'post_id' => $postID,
		'meta_key' => $column,
		'meta_value' => $value
		)
	);
}

function getRelationItem($items = array()){
	if(!empty($items)){
		$args = array(
			'post_type'  => 'item',
			'meta_query' => array(
				array(
					'key'     => 'item_id',
					'value'   => $items,
					'compare' => 'IN',
				),
			),
			'fields' => 'ids'
		);
		$relationItems = new WP_Query( $args );
		return json_encode($relationItems->posts);
	}
	return null;
}