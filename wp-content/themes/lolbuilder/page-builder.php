<?php
get_header();
?>
<div class="build-section champions-section row">
	<h2>1 - Pick a champion!</h2>
	<div class="chosen-champion">
		
		<script type="text/html" id="chosenChampionTpl">
		   
		</script>
	</div>
	<h3>Champions</h3>
	<div class="champions-selector row">
	<?php
		$championStatsFields = get_post_custom_keys('2987');

		if ( false === ( $champions = get_transient( 'wp_query_champions') ) ) {
			$args = array(
			    'post_type' => 'champion',
			    'posts_per_page'    =>  -1,
				'order' => 'ASC',
				'orderby' => 'title'
			);
			$champions = new WP_Query( $args );
			set_transient( 'wp_query_champions', $champions, 180 );
		}	
		while($champions->have_posts()): $champions->the_post();

		if ( false === ( $championStats = get_transient( 'championStats_'.$post->post_name ) ) ) {
			$championStats = '';
			foreach ($championStatsFields as $key => $field) {		
				if(substr( $field, 0, 6 ) === "field_"){
					$fieldOject = get_field_object($field, '2987');
					$championStats .= ' data-'.$fieldOject['name'].'="'.get_field($fieldOject['name']).'"';
				}
			}
			set_transient( 'championStats_'.$post->post_name, $championStats, 180 );
		}		
	?>
	<div class="champion-list" <?php echo $championStats; ?> data-name="<?php the_title(); ?>" <?php echo $championStats; ?> >
		<div class="champion-list-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
		<div class="champion-list-name"><?php the_title(); ?></div>
		
		<div class="champion-list-preview">
			<div class="champion-list-preview-thumbnail"><?php the_post_thumbnail(); ?></div>
			<div class="champion-list-preview-name"><?php the_title(); ?></div>
			<div class="champion-list-stats-preview">
				
			</div>
		</div>
	</div>
	<?php
		endwhile;
	?>				
	</div>
</div>
<div class="build-section items-section row">
	
	<div class="items-build">		
		<h2>2 - Choose your items</h2>
		<div class="items-build-wrapper">
			<div class="item-builder-placeholders">
				<div class="item-slot"></div>
				<div class="item-slot"></div>
				<div class="item-slot"></div>
				<div class="item-slot"></div>
				<div class="item-slot"></div>
				<div class="item-slot"></div>
			</div>
			<div class="items-builder-list">			
			</div>
		</div>
	</div>

	<div class="">
		<button class="items-selector-toggle expanded dropdown button">Items selector</button>
	</div>
	<div class="items-selector">
		<?php
		$groupID='437';
		$custom_field_keys = get_post_custom_keys($groupID);
		sort($custom_field_keys);
		?>
		<form id="statsFilters">
			<div class="row">
				<div class="large-12 column">			
					<h3>Stats filters</h3>
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
		if ( false === ( $items = get_transient( 'wp_query_items') ) ) {
			$args = array(
			    'post_type' => 'item',
			    'posts_per_page'    =>  -1,
				'order' => 'ASC',
				'orderby' => 'title'
			);
			$items = new WP_Query( $args );
			set_transient( 'wp_query_items', $items, 180 );
		}	
		?>
		<div class="header-list-items row">
			<h3 class="column large-6">Items</h3>
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
				<div class="item-list-preview">
					<div class="item-list-preview-thumbnail"><?php the_post_thumbnail(); ?></div>
					<div class="item-list-preview-name"><?php the_title(); ?></div>
					<div class="item-list-preview-description">
						<?php the_content(); ?>
					</div>
					<div class="item-list-gold-preview">
						<?php the_field('gold_total'); echo ' <small>('.get_field('gold_base').')</small>'; ?>
					</div>
				</div>
			</div>
			<?php
		endwhile;
		?>
		</div>
		
	</div>

</div>


<?php get_footer(); ?>