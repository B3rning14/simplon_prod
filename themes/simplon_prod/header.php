<!DOCTYPE html>
<html>
  <head>
    <title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php bloginfo('description'); ?>" />
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
    <!-- nav -->
    <nav class="nav" data-visible="0" role="navigation">
      <?php theme_nav(); ?>
    </nav>
    <!-- /nav -->
    <!--main-->
    <main>