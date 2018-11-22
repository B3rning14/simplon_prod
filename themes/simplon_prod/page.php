<?php 
  get_header(); 
?>
<?php
  if(current_user_can('administrator'))
  {
?>
<main>
  <section>
    <h2><?php the_title(); ?></h2>
    <?php if (have_posts()): while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
    <?php endwhile; ?>
    <?php else: ?>
      <h2><?php _e( 'Sorry, nothing to display.', 'simplon_prod' ); ?></h2>
    <?php endif; ?>
  </section>
</main>
<?php
  }
  else
  {
?> 
  <h1>Accès Interdit</h1>
  <p>Vous devez être administrateur pour accéder à cette page</p>
<?php
  }
?>
<?php get_footer(); ?>