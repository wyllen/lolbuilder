<?php
get_header();
?>
<div class="build-section champions-section row">
	<h2>1 - Pick a champion!</h2>
	<div class="chosen-champion">
		<script type="text/html" id="chosenChampionTpl">
			<div class="chosen-champion-tpl">
				<div class="row">
					<div class="column large-9">
						<div class="row">
							<div class="column large-2">
								<div class="chosen-champion-thumbnail">
									<img src="" class="chosen-champion-thumbnail-img" alt="champion img">
								</div>
							</div>
							<div class="column large-10">
								<ul class="chosen-champion-stats row large-up-3">
								</ul>
							</div>
						</div>
					</div>

					<div class="column large-3">
						<canvas id="championChart" width="320px" height="320px"></canvas>
					</div>
				</div>
				<div class="row">
					<div class="large-12">
						<div class="champion-bar-charts">
						</div>
					</div>
					<div class="large-12">
					<div class="levelSelector-wrapper">
						<div class="text-center"><strong>Level:</strong></div>
						<input type="range" value="1" max="18" min="1" step="1" class="levelSelector"/>
						<ul class="levelSelector-levels">
							<li>1</li>
							<li>2</li>
							<li>3</li>
							<li>4</li>
							<li>5</li>
							<li>6</li>
							<li>7</li>
							<li>8</li>
							<li>9</li>
							<li>10</li>
							<li>11</li>
							<li>12</li>
							<li>13</li>
							<li>14</li>
							<li>15</li>
							<li>16</li>
							<li>17</li>
							<li>18</li>
						</ul>
					</div>
					</div>
				</div>
			</div>				   
		</script>

	</div>	
	<div>
		<button class="champions-selector-toggle expanded dropdown button">Champions selector</button>
	</div>
	<div class="champions-selector row">
	<?php
		$championStatsFields = get_post_custom_keys('2987');
			$championStatsArray = array();
			foreach ($championStatsFields as $key => $field) {		
				if(substr( $field, 0, 6 ) === "field_"){
					$fieldOject = get_field_object($field, '2987');
					if($fieldOject['type'] != 'tab'){
						$type= 'infos-';
						if($fieldOject['type'] != 'number'){
							$type= 'base-';
						}
						$championStatsArray['data-'.$type.$fieldOject['name']] = $fieldOject['label'];
					}
				}
			}
		?>
		<script>
		 var championsStats = <?php echo json_encode($championStatsArray); ?>;
		</script>
		<?php

		if ( false === ( $champions = get_transient( 'wp_query_champions3') ) ) {
			$args = array(
			    'post_type' => 'champion',
			    'posts_per_page'    =>  -1,
				'order' => 'ASC',
				'orderby' => 'title'
			);
			$champions = new WP_Query( $args );
			set_transient( 'wp_query_champions3', $champions, 1800 );
		}	
		while($champions->have_posts()): $champions->the_post();

		if ( false === ( $championStats = get_transient( 'championStats8_'.$post->post_name ) ) ) {
			$championStats = '';
			$championStatsArray = array();
			foreach ($championStatsFields as $key => $field) {		
				if(substr( $field, 0, 6 ) === "field_"){
					$fieldOject = get_field_object($field, '2987');
					if($fieldOject['type'] != 'tab'){
						$type= 'infos-';
						if($fieldOject['type'] != 'number'){
							$type= 'base-';
						}
						$championStats .= ' data-'.$type.$fieldOject['name'].'="'.get_field($fieldOject['name']).'"';
						$championStatsArray['data-'.$type.$fieldOject['name']] = get_field($fieldOject['name']);
					}
				}
			}
			set_transient( 'championStats8_'.$post->post_name, $championStats, 1800 );
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