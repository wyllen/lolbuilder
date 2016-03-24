<?php
get_header();
?>
<div class="build-section champions-section row">
	<h2>1 - Pick a champion!</h2>
	<div class="chosen-champion">
		<div class="row">
			<div class="column large-9">
				<div class="row">
					<div class="column large-3">
						<div class="chosen-champion-thumbnail">
							<img src="" alt="champion img">
						</div>
					</div>
					<div class="column large-9">
						<ul class="chosen-champion-stats row large-up-3">
							<li class="column"><strong>Stats: </strong><span>10</span></li>
							<li class="column"><strong>Stats: </strong><span>10</span></li>
							<li class="column"><strong>Stats: </strong><span>10</span></li>
							<li class="column"><strong>Stats: </strong><span>10</span></li>
							<li class="column"><strong>Stats: </strong><span>10</span></li>
							<li class="column"><strong>Stats: </strong><span>10</span></li>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="large-12">
						<div class="champion-bar-charts">
							<div class="champion-bar-chart">
								<div class="champion-bar-chart-wrapper">
									<div class="champion-bar-chart-total"  style="height:20%">
										<div class="champion-bar-chart-base" style="height:50%"><span>30</span></div>
										<div class="champion-bar-chart-items" style="height:50%"><span>30</span></div>
									</div>
								</div>
								<div class="champion-bar-chart-value">60</div>
								<div class="champion-bar-chart-label">Armor</div>
							</div>
							<div class="champion-bar-chart">
								<div class="champion-bar-chart-wrapper">
									<div class="champion-bar-chart-total"  style="height:70%">
										<div class="champion-bar-chart-base" style="height:60%"><span>2000</span></div>
										<div class="champion-bar-chart-items" style="height:40%"><span>1500</span></div>
									</div>
								</div>
								<div class="champion-bar-chart-value">3500</div>
								<div class="champion-bar-chart-label">Health</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="column large-3">
				<canvas id="championChart" width="320px" height="320px"></canvas>
			</div>
		</div>
		<script type="text/html" id="chosenChampionTpl">
		   
		</script>
	</div>	
	<div>
		<button class="champions-selector-toggle expanded dropdown button">Champions selector</button>
	</div>
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
			set_transient( 'wp_query_champions', $champions, 1800 );
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
			set_transient( 'championStats_'.$post->post_name, $championStats, 1800 );
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

	<div>
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
			set_transient( 'wp_query_items', $items, 1800 );
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
				set_transient( 'itemStats_'.$post->post_name, $itemStats, 1800 );
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