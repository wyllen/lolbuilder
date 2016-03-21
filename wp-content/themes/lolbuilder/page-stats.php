<?php
header( 'Content-type: text/html; charset=utf-8' );

$aContext = array(
    'http' => array(
        'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);


populateStatsField($cxContext);
