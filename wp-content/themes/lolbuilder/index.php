<?php

$aContext = array(
    'http' => array(
        'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);

/*
----------------------------------------------------------------------
----------------------------------------------------------------------
----------------------------------------------------------------------
----------------------------MAPS-------------------------------------
----------------------------------------------------------------------
----------------------------------------------------------------------
----------------------------------------------------------------------
 */

if ( false === ( $maps = get_transient( 'maps' ) ) ) {
	$maps = json_decode(file_get_contents('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/map?api_key=a89dd7d8-92ca-43d3-ac96-8f87d203a542', false, $cxContext));
	set_transient( 'maps', $maps );
}
/*
----------------------------------------------------------------------
----------------------------Populate maps-----------------------------
----------------------------------------------------------------------
 */
echo '<br><br> ----------------------------------------------------------------------<br>
----------------------------Populate maps----------------------------<br>
----------------------------------------------------------------------<br>';
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
    }
}

/*
----------------------------------------------------------------------
----------------------------------------------------------------------
----------------------------------------------------------------------
----------------------------ITEMS-------------------------------------
----------------------------------------------------------------------
----------------------------------------------------------------------
----------------------------------------------------------------------
 */

/*
----------------------------------------------------------------------
----------------------------Static Data----------------------------
----------------------------------------------------------------------
 */
if ( false === ( $items = get_transient( 'items' ) ) ) {
	$items = json_decode(file_get_contents('http://ddragon.leagueoflegends.com/cdn/6.5.1/data/en_US/item.json', false, $cxContext));
	set_transient( 'items', $items );
}
/*
----------------------------------------------------------------------
----------------------------Static Data Translate----------------------------
----------------------------------------------------------------------
 */
if ( false === ( $translates = get_transient( 'translates' ) ) ) {
	$translates = json_decode(file_get_contents('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/language-strings?locale=en_US&api_key=a89dd7d8-92ca-43d3-ac96-8f87d203a542', false, $cxContext));
	set_transient( 'translates', $translates );
}
/*
----------------------------------------------------------------------
----------------------------Populate stats fields------------------
----------------------------------------------------------------------
 */
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
            $i++;
    }

    $translates = $translates->data;
    foreach ($translates as $translateSlug => $translate) {
        if (isset($fields[$translateSlug])) {
            $fields[$translateSlug]['label'] = $translate;
            echo $translateSlug . ' Stat a été traduit par '.$translate.'<br>';
        }
    }


    // register_field_group(
    // 	array(
    //     'key'                   => 'stats',
    //     'title'                 => 'Stats',
    //     'fields'                => $fields,
    //     'location'              => array(
    //         array(
    //             array(
    //                 'param'    => 'post_type',
    //                 'operator' => '==',
    //                 'value'    => 'item',
    //             ),
    //         ),
    //     ),
    //     'menu_order'            => 0,
    //     'position'              => 'normal',
    //     'style'                 => 'default',
    //     'label_placement'       => 'top',
    //     'instruction_placement' => 'label',
    //     'hide_on_screen'        => '',
    // )
    // 	);

foreach ($fields as $fieldSlug => $fieldValues) {
    $acfFunctions = New acf_field_functions();
    $acfFunctions->update_field($fieldValues, 437);
    wp_cache_delete( 'load_field/key=' . $fieldValues['key'], 'acf' );
}
        



/*
----------------------------------------------------------------------
----------------------------Populate items----------------------------
----------------------------------------------------------------------
 */
echo '<br><br> ----------------------------------------------------------------------<br>
----------------------------Populate items----------------------------<br>
----------------------------------------------------------------------<br>';
foreach ($items->data as $itemID => $item) {
    $itemPost = get_page_by_title($item->name, OBJECT, 'item');
    if (is_null($itemPost)) {
        echo $item->name . ' Item a été créé <br>';
        $itemPostID = wp_insert_post(
            array(
                'post_title'   => $item->name,
                'post_status'  => 'publish',
                'post_type'    => 'item',
                'post_excerpt' => $item->plaintext,
            ));
        add_post_meta($itemPostID, 'item_id', $itemID);
        add_post_meta($item->into, 'used_to_craft', $itemID);
        add_post_meta($item->from, 'crafted_with', $itemID);
        add_post_meta($item->gold->base, 'gold_base', $itemID);
        add_post_meta($item->gold->total, 'gold_total', $itemID);
        add_post_meta($item->depth, 'depth', $itemID);
    }
}
