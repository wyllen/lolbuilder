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
		return $relationItems->posts;
	}
	return null;
}

function getRelationMaps($maps = array()){
	if(!empty($items)){
		$mapsIds = array();
		foreach ($maps as $mapId => $map) {
			if($map){
				$mapsIds[] = $mapId;
			}
		}
		$args = array(
			'post_type'  => 'map',
			'meta_query' => array(
				array(
					'key'     => 'map_id',
					'value'   => $mapsIds,
					'compare' => 'IN',
				),
			),
			'fields' => 'ids'
		);
		$relationMaps = new WP_Query( $args );
		return $relationMaps->posts;
	}
	return null;
}