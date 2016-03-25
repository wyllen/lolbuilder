<?php

function getApiData($api, $cxContext = false){
	if ( false === ( $apiData = get_transient( $api ) ) ) {
		switch ($api) {
			case 'item':
					$apiData = json_decode(file_get_contents('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/item?locale=en_US&itemListData=all&api_key=a89dd7d8-92ca-43d3-ac96-8f87d203a542', false, $cxContext));
				break;
			case 'map':
					$apiData = json_decode(file_get_contents('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/map?locale=en_US&api_key=a89dd7d8-92ca-43d3-ac96-8f87d203a542', false, $cxContext));
				break;
			case 'champion':
					$apiData = json_decode(file_get_contents('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?locale=en_US&champData=all&api_key=a89dd7d8-92ca-43d3-ac96-8f87d203a542', false, $cxContext));
				break;	
			case 'language':
					$apiData = json_decode(file_get_contents('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/language-strings?locale=en_US&api_key=a89dd7d8-92ca-43d3-ac96-8f87d203a542', false, $cxContext));
				break;
			default:
				return flase;
				break;
		}
		set_transient( $api, $apiData, 180 );
	}
	return $apiData;
}

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

function getItemsPost($cxContext = null){
		$items = getApiData('item', $cxContext);
		$itemsArray = array();
		foreach ($items->data as $itemID => $item) {
			$item->name = str_replace(':', ' ', $item->name);
			$itemPost = get_page_by_title($item->name, OBJECT, 'item');
			if (!is_null($itemPost)) {
				$itemsArray[$itemID] = $itemPost->ID;
			}
		}
		return $itemsArray;
}


// function getChampionsPost($cxContext = null){
// 		$champions = getApiData('champion', $cxContext);
// 		$championsArray = array();
// 		foreach ($champions->data as $championName => $champion) {
// 			$championPost = get_page_by_title($championName, OBJECT, 'champion');
// 			if (!is_null($championPost)) {
// 				$championsArray[$championID] = $championPost->ID;
// 			}
// 		}
// 		return $championsArray;
// }

function getMapsPost($cxContext = null){		
		$maps = getApiData('map', $cxContext);
		$mapsArray = array();
		foreach ($maps->data as $mapID => $map) {
		    $mapPost = get_page_by_title($map->mapName, OBJECT, 'map');
		    if (!is_null($mapPost)) {
		        $mapsArray[$mapID]=$mapPost->ID;
		    }
		}
		return $mapsArray;
}


function getRelationItem($items = array(), $itemsArray = array()){
	var_dump($items);
	if(!empty($items)){
		$relationItems = array();
		foreach ($items as $key => $value) {
			if(!is_null($itemsArray[$value])){
				$relationItems[] = $itemsArray[$value];
			}
		}
		var_dump($relationItems);
		return $relationItems;
	}
	return null;
}

function getRelationMaps($maps = array(), $mapsArray = array()){
	if(!empty($maps)){
		$relationmaps = array();
		foreach ($maps as $key => $value) {
			if($value){
				$relationmaps[] = $mapsArray[$key];				
			}
		}
		return $relationmaps;
	}
	return null;
}

function Generate_Featured_Image( $image_url, $post_id  ){
	$aContext = array(
	    'http' => array(
	        'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
	        'request_fulluri' => true,
	    ),
	);
	$cxContext = stream_context_create($aContext);

    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url, false, $cxContext);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
    else                                    $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}

function populateMaps($cxContext = null){

	$maps = getApiData('map', $cxContext);

	echo '<br><br> ----------------------------------------------------------------------<br>
	----------------------------Populate maps----------------------------<br>
	----------------------------------------------------------------------<br>';
	$mapsArray = array();
	foreach ($maps->data as $mapID => $map) {
	    $mapPost = get_page_by_title($map->mapName, OBJECT, 'map');
	    if (is_null($mapPost)) {
	        $mapPostID = wp_insert_post(
	            array(
	                'post_title'  => $map->mapName,
	                'post_status' => 'publish',
	                'post_type'   => 'map',
	            ));
	        add_post_meta($mapPostID, 'map_id', $mapID);
	        echo $map->mapName . ' Map a été créée <br>';
	        ob_flush();flush();
	        $mapsArray[$mapID]=$mapPostID;
	    }else{
	        $mapsArray[$mapID]=$mapPost->ID;
	    }
	}

	return $mapsArray;

}


function populateStatsField($cxContext = null){

		$items = getApiData('item', $cxContext);

		$translates = getApiData('language', $cxContext);

		echo '<br><br> ----------------------------------------------------------------------<br>
		----------------------------Populate stats----------------------------<br>
		----------------------------------------------------------------------<br>';


		    $fields = array();
		    $i=1000;
		    foreach ($items->basic->stats as $statsSlug => $stat) {
		            wp_insert_term($statsSlug, 'stat-type');
		            $fields[$statsSlug] = array(
		                'key'   => 'field_'.$statsSlug,
		                'label' => $statsSlug,
		                'name'  => $statsSlug,
		                'type'  => 'number',
		                'order_no' => $i
		            );
		            echo $statsSlug . ' Stat a été créé <br>';
		            ob_flush();flush();
		            $i++;
		    }

		    $translates = $translates->data;
		    foreach ($translates as $translateSlug => $translate) {
		        if (isset($fields[$translateSlug])) {
		            $fields[$translateSlug]['label'] = $translate;
		            echo $translateSlug . ' Stat a été traduit par '.$translate.'<br>';
		            ob_flush();flush();
		        }
		    }


		foreach ($fields as $fieldSlug => $fieldValues) {
		    $acfFunctions = New acf_field_functions();
		    $acfFunctions->update_field($fieldValues, 437);
		    wp_cache_delete( 'load_field/key=' . $fieldValues['key'], 'acf' );
		}
		        
}


function populateItemsPosts($cxContext = null){
	global $wpdb;

	$items = getApiData('item', $cxContext);	

	echo '<br><br> ----------------------------------------------------------------------<br>
	----------------------------Populate items----------------------------<br>
	----------------------------------------------------------------------<br>';
	//$i =0;
	//var_dump($items->data);
	$itemsArray = array();
	foreach ($items->data as $itemID => $item) {
		//if($i<=10){

		$item->name = str_replace(':', ' ', $item->name);
		$itemPost = get_page_by_title($item->name, OBJECT, 'item');


		if (is_null($itemPost)) {
		    $itemPostID = wp_insert_post(
		        array(
		            'post_title'   => $item->name,
		            'post_status'  => 'publish',
		            'post_type'    => 'item',
		            'post_excerpt' => $item->plaintext,
		          	'post_content' => $item->description
		        ));
		    echo $item->name . ' <br>-----------Item a été créé-----------<br>';
		    ob_flush();flush();
		}else{
		    $itemPostID = $itemPost->ID;
		     $my_post = array(
		          'ID'           => $itemPostID,
		          'post_content' => $item->description,
		      );
		      wp_update_post( $my_post );
		    echo $item->name . '<br>-----------IItem a été mis a jour-----------<br>';
		    ob_flush();flush();
		}
		var_dump($item->description);
		$wpdb->update($wpdb->posts, array("post_content"=>$item->description), array("ID"=>$itemPostID), array("%s"), array("%d"));
		update_meta_value('item_id', $itemID, $itemPostID);
		$itemsArray[$itemID] = $itemPostID;
	}

	return $itemsArray;
		        
}


function populateChampionPosts($cxContext = null){
	global $wpdb;

	$champions = getApiData('champion', $cxContext);	

	echo '<br><br> ----------------------------------------------------------------------<br>
	----------------------------Populate champions----------------------------<br>
	----------------------------------------------------------------------<br>';

	$championsArray = array();
	foreach ($champions->data as $championKey => $champion) {
		$championName = $champion->name;
		$championPost = get_page_by_title($championName, OBJECT, 'champion');


		if (is_null($championPost)) {
		    $championPostID = wp_insert_post(
		        array(
		            'post_title'   => $championName,
		            'post_status'  => 'publish',
		            'post_type'    => 'champion',
		            'post_excerpt' => $champion->blurb,
		          	'post_content' => $champion->blurb
		        ));
		    echo $championName . ' <br>-----------champion a été créé-----------<br>';
		    ob_flush();flush();
		}else{
		    $championPostID = $championPost->ID;
		     $my_post = array(
		          'ID'           => $championPostID,
		          'post_content' => $champion->blurb,
		      );
		      wp_update_post( $my_post );
		    echo $championName . '<br>-----------champion a été mis a jour-----------<br>';
		    ob_flush();flush();
		}
		$championsArray[$championID] = $championPostID;
	}

	return $championsArray;		        
}

function populateChampionsFields( $cxContext = null){

	$champions = getApiData('champion', $cxContext);

	foreach ($champions->data as $championKey => $champion) {
		$championName = $champion->name;
		$championPost = get_page_by_title($championName, OBJECT, 'champion');
		$championPostID = $championPost->ID;
		foreach ($champion->info as $key => $value) {
			update_meta_value($key , $value, $championPostID);    
		}
		foreach ($champion->stats as $key => $value) {
			update_meta_value($key , $value, $championPostID);    
		}		
		//Generate_Featured_Image( 'http://ddragon.leagueoflegends.com/cdn/6.6.1/img/champion/'.$champion->image->full, $championPostID );
		echo $championName . ' Stats mis à jour <br>';  
		ob_flush();flush();  
	}
		        
}

function updateItemsContent(){
	$aContext = array(
        'http' => array(
            'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
            'request_fulluri' => true,
        ),
    );
    $cxContext = stream_context_create($aContext);

	$items = getApiData('item', $cxContext);


	echo '<br><br> ----------------------------------------------------------------------<br>
	----------------------------Populate items----------------------------<br>
	----------------------------------------------------------------------<br>';

	foreach ($items->data as $itemID => $item) {
		var_dump($item->name);
		var_dump($item->description);
		$item->name = str_replace(':', ' ', $item->name);
		$itemPost = get_page_by_title($item->name, OBJECT, 'item');

		if (is_null($itemPost)) {
		}else{
		    $itemPostID = $itemPost->ID;
		     $my_post = array(
		          'ID'           => $itemPostID,
		          'post_content' => $item->description,
		      );
		      wp_update_post( $my_post );
		    echo $item->name . '<br>-----------IItem a été mis a jour-----------<br>';
		    ob_flush();flush();
		}
		update_meta_value('item_id', $itemID, $itemPostID);
	}
		        
}

function populateItemsFields($itemsArray = null, $cxContext = null, $fields = array() , $mapsArray = null){


	if(empty($fields)){
		$fields = array('into' => 'field_56ebca3fef24a','from' => 'field_56ebcc69ef24d', 'maps' => 'field_56ebd4224fe67', 'gold/base' => 'gold_base', 'gold/total' => 'gold_total', 'depth' => 'depth', 'stats'=>'stats');
	}
	$items = getApiData('item', $cxContext);

		foreach ($items->data as $itemID => $item) {

	        $itemPostID = $itemsArray[$itemID];

	        if($itemPostID != 0){
	        	foreach ($fields as $key => $field) {
	        		echo '<h1 style="color:orange;">Mise a jour du champs '.$key.'</h1>'; 
	        		if($field == 'stats'){
						$itemStats = get_object_vars($item->stats);
						foreach ($itemStats as $stat => $statValue) {
							update_meta_value($stat, $statValue, $itemPostID);    
							echo $item->name . ' Field(stat) '.$stat.' mis à jour <br>';  
							ob_flush();flush();      
						}
	        		}elseif ($key == 'into' || $key == 'from') {
			            update_field( $field, getRelationItem($item->{$key}, $itemsArray), $itemPostID );
			             echo $item->name . ' Field(into/from) '.$key.' mis à jour <br>';
			            ob_flush();flush();	        			
	        		}elseif ($key == 'maps') {
			            update_field( $field, getRelationMaps($item->{$key}, $mapsArray), $itemPostID );
			            echo $item->name . ' Field maps mis à jour <br>';
			            ob_flush();flush();
	        		}else{
	        			if(strpos($key, '/') !== false){
	        				$fieldKeys = explode('/', $key);
	        				if(isset($item->{$fieldKeys[0]}->{$fieldKeys[1]})){
	        					$fieldValue = $item->{$fieldKeys[0]}->{$fieldKeys[1]};
	        				}else{
	        					$fieldValue = null;
	        				}
	        			}else{
	        				if(isset($item->{$key})){
	        					$fieldValue = $item->{$key};
	        				}else{
	        					$fieldValue = null;
	        				}
	        			}
	        			if(!is_null($fieldValue)){
		            		update_meta_value($field, $fieldValue, $itemPostID);
				            echo $item->name . ' Field(other) '.$field.' mis à jour <br>';
				            ob_flush();flush();
	        			}
	        		}
	        	}	          

	        }

		}

	

		        
}

/**



AJAX



**/


	add_action( 'wp_ajax_update-field'.$key, function() {
		$key = $_POST['key'];
		$field = $_POST['field'];
		$aContext = array(
		    'http' => array(
		        'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
		        'request_fulluri' => true,
		    ),
		);
		$cxContext = stream_context_create($aContext);
		if ( false === ( $itemsArray = get_transient( 'itemsArray' ) ) ) {
			$itemsArray = getItemsPost($cxContext);
			set_transient( 'itemsArray', $itemsArray, 180);
		}

		if ($key == 'maps') {
			if ( false === ( $mapsArray = get_transient( 'mapsArray' ) ) ) {
				$mapsArray = getMapsPost($cxContext);
				set_transient( 'mapsArray', $mapsArray, 180);
			}
			populateItemsFields($itemsArray, $cxContext, array($key => $field), $mapsArray);
		}else{
			populateItemsFields($itemsArray, $cxContext, array($key => $field));
		}

	}
	);

