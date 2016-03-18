<?php get_header(); ?>
<main role="main">

    <?php if( have_posts() ): while( have_posts() ): the_post(); ?>
    <article role="article">
    	<header>
    		 <h1><?php the_title(); ?></h1>
    	</header>       
        <section class="entry-content">
            <?php the_content(); ?>
        </section>
    </article>
    <?php endwhile; endif; ?>

</main>
<?php get_footer(); ?>