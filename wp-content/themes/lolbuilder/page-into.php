<?php
header( 'Content-type: text/html; charset=utf-8' );

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

/*
----------------------------------------------------------------------
----------------------------Populate items FIELDS---------------------
----------------------------------------------------------------------
 */
populateItemsFields($itemsArray, $cxContext, array('into' => 'field_56ebca3fef24a'));
