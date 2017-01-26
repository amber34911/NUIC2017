<?php /** * The template for displaying all pages. * * This is the template that displays all pages by default. * * @package ThinkUpThemes */ get_header(); ?>
<style>
.left{
    padding-right:60px;
    display:inline-block;
    float:left;
}
.right{
    padding-right:60px;
    display:inline-block;
    float:left;
}
.clear{
    clear:both;
}
.announce{
    display:inline-block;
    text-align:left;
}
.announce_container{
    text-align:center;
}
</style>
<h2>最新公告</h2>
<hr>
<div class="announce_container">
<div class="announce">
<?php $frank=0; query_posts( 'posts_per_page=9&paged=0&orderby=time&category_name=公告'); if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php if($frank==0){echo "<div class='left'>";}?>
<?php if($frank==5){echo "</div><div class='right'>";}?>



    <div class="list">
        <span class="date" <?php echo ($frank<3?"style='color:red;'":"");?>><?php echo get_the_date(); ?></span>
        <span class="title">
        <a href="<?php the_permalink();?>" title="<?php the_title();?>" <?php echo ($frank<3?"style='color:red'":"");?>>
            <?php the_title() ?>
        </a>
        </span>
    </div>

<?php $frank++;endwhile; endif;?>
<div class="list">
        <span class="date"></span>
        <span class="title">
        <a href="../list" title="所有公告列表">
            所有公告列表
        </a>
        </span>
    </div>
</div>
<div class="clear"></div>
</div>
</div>

<?php get_footer(); ?>