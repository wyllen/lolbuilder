<?php

$aContext = array(
    'http' => array(
        'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);
$items     = json_decode(file_get_contents('http://ddragon.leagueoflegends.com/cdn/6.5.1/data/fr_FR/item.json', false, $cxContext));

// var_dump($items->data);
$i = 0;
foreach ($items->data as $key => $item) {
    if (is_null(get_page_by_title($item->name, OBJECT, 'item'))) {
    	//if($i < 10){
    		echo $item->name.' a été créé <br>';
	        $post_id = wp_insert_post(
	            array(
	                'post_title'  => $item->name,
	                'post_status' => 'publish',
	                'post_type'   => 'item',
	            ));
	        $i++;
    	//}
    }
}
