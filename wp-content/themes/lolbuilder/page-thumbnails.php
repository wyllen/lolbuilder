<?php


$aContext = array(
    'http' => array(
        'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);

if ( false === ( $items = get_transient( 'items' ) ) ) {
	$items = json_decode(file_get_contents('http://ddragon.leagueoflegends.com/cdn/6.5.1/data/en_US/item.json', false, $cxContext));
	set_transient( 'items', $items );
}

foreach ($items->data as $itemID => $item) {

        $item->name = str_replace(':', ' ', $item->name);
        $itemPost = get_page_by_title($item->name, OBJECT, 'item');
        if (!is_null($itemPost)) {
        	Generate_Featured_Image( 'http://ddragon.leagueoflegends.com/cdn/6.5.1/img/item/'.$item->image->full, $itemPost->ID );
            echo $item->name . '<br>-----------Image récupérée-----------<br>';
            ob_flush();flush();
        }
}