<?php
get_header();

$fieldsToAjax = array('into' => 'field_56ebca3fef24a','from' => 'field_56ebcc69ef24d', 'maps' => 'field_56ebd4224fe67', 'gold/base' => 'gold_base', 'gold/total' => 'gold_total', 'depth' => 'depth', 'stats'=>'stats');

$aContext = array(
            'http' => array(
                'proxy'           => 'nrs-proxy.ad-subs.w2k.francetelecom.fr:3128',
                'request_fulluri' => true,
            ),
        );
        $cxContext = stream_context_create($aContext);
populateItemsPosts($aContext);
?>
<script>
    jQuery(document).on('ready',function($){
        $ = jQuery;
        <?php 
        foreach ($fieldsToAjax as $key => $fieldToAjax):
        ?>
        var data = {
            action: 'update-field',
            key: '<?php echo $key; ?>',
            field: '<?php echo $fieldToAjax; ?>',
        };
        $.post(ajaxurl, data, function(response){
        })
        .done( function(response) { 
             $('body').append('<h1 style="color:red;"><?php echo $key; ?></h1>');
             $('body').append(response);
        })
        .fail(function() {
                $('body').append('Erreur sur update-<?php echo $key; ?>');
        })
        <?php endforeach; ?>
    })
</script>
<?php get_footer();?>