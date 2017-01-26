<?php

get_header(); ?>
	<style>
	.list_right{
		margin-right:10px;
	}
	.category_list{
		padding-left:100px;
	}
	</style>
	<div id="primary" class="site-content">
		<div id="content" role="main">
<div id="page-allpost">
<ul class="category_list">

<?php 
	$count_posts = wp_count_posts(); $published_posts = $count_posts->publish;
    query_posts( 'posts_per_page=-1&category_name=announcement' );
    while ( have_posts() ) : the_post(); ?>
		<li><span class="list_right"><?php echo get_the_date(); ?></span><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permalink to <?php the_title(); ?>"  class="list_left"><?php the_title(); ?></a>
		</li>
<?php endwhile; ?>

</ul>
 </div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>
