<?php
header( 'Content-type: text/html; charset=utf-8' );

$aContext = array(
    'http' => array(
        'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);

if ( false === ( $mapsArray = get_transient( 'mapsArray' ) ) ) {
	$mapsArray = getMapsPost($cxContext);
	set_transient( 'mapsArray', $mapsArray, 180);
}

/*
----------------------------------------------------------------------
----------------------------Populate items FIELDS---------------------
----------------------------------------------------------------------
 */
populateItemsFields(null, $cxContext, array('maps' => 'field_56ebd4224fe67'), $mapsArray );
