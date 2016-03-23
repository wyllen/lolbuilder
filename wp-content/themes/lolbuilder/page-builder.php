<?php
get_header();
$groupID='437';
$custom_field_keys = get_post_custom_keys($groupID);
sort($custom_field_keys);
?>
<form id="statsFilters">
	<div class="row">
		<div class="large-12 column">			
			<h2>Stats filters</h2>
		</div>
	</div>
	<div class="row">
		<div class="large-4 column">
			<select class="select-stats-filters">
			<?php
			foreach ($custom_field_keys as $key => $field):
				if(substr( $field, 0, 6 ) === "field_" && strpos($field, 'PerLevel') === false):
				$fieldOject = get_field_object($field, $groupID);
			?>
				<option value="<?php echo $fieldOject['key']; ?>"><?php echo $fieldOject['label']; ?></option>
			<?php
			 	endif;
			endforeach;
			 ?>				
			</select>
			<button class="add-stat-filter">+</button>
		</div>
		<div class="large-8 column">
			<div class="row list-stats-filters large-up-2">
				<script type="text/html" id="statFilterTpl">
				   <div class="column field-filter">
						<div class="row field-filter-content">
							<div class="column large-6">
								<div class="column-field-filter-name"></div>
							</div>
							<div class="column large-6">
								<div className="row expanded">
									<div class="column large-6 column-field-filter-compare">
										<select class="filter-select-compare">
											<option value="equal">=</option>
											<option value="sup">></option>
											<option value="low"><</option>
										</select>
									</div>
									<div class="column large-6 column-field-filter-value">
										<input type="text" class="filter-input-name">
									</div>
								</div>
							</div>
							<div class="remove-filter">-</div>
						</div>
					</div>
				</script>
			</div>
		</div>
		
	</div>	
</form>
<?php
$args = array(
    'post_type' => 'item',
    'posts_per_page'    =>  -1,
	'order' => 'ASC',
	'orderby' => 'title'
);
$items = new WP_Query( $args );
?>
<div class="header-list-items row">
	<h2 class="column large-6">Items</h2>
	<div class="list-items-sort column large-4 text-right">
		<select class="sort-by-select">
			<option value="name">Sort by name</option>
			<option value="gold">Sort by cost</option>
		</select>
	</div>
</div>
<div class="list-items row">
	<?php
	while($items->have_posts()): $items->the_post();
	if ( false === ( $itemStats = get_transient( 'itemStats_'.$post->post_name ) ) ) {
		$itemStats = '';
		foreach ($custom_field_keys as $key => $field){
			if(substr( $field, 0, 6 ) === "field_"){
				$statValue = get_field($field,$item->ID);
				if($statValue!=''){
					if(strpos($field, 'Percent') === false && strpos($field, 'Chance') === false){
						$itemStats .= ' '.$field.'="'.$statValue.'"';
					}else{
						$itemStats .= ' '.$field.'="'.($statValue*100).'"';
					}
				}
			}
		}
		set_transient( 'itemStats_'.$post->post_name, $itemStats, 180 );
	}
	?>
	<div class="item-list" <?php echo $itemStats; ?> data-name="<?php the_title(); ?>" data-gold="<?php the_field('gold_total'); ?>">
		<div class="item-list-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
		<div class="item-list-name"><?php the_title(); ?></div>
		<div class="item-list-gold">
			<?php the_field('gold_total'); echo ' <small>('.get_field('gold_base').')</small>'; ?>
		</div>
	</div>
	<?php
endwhile;
?>
</div>
<?php get_footer(); ?>