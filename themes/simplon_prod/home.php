<?php 
  get_header();
  $pages = get_pages(['sort_column' => 'ID']);
  $bgimg = get_theme_mod('simplon_prod_theme_custom_background_image_setting',get_template_directory_uri().'/img/welcome.jpg');
?>
<!-- home -->
<section id="home" style="background:url('<?php echo $bgimg; ?>');">
  <div class="acrylic">
    <div class="container">
      <fieldset>
        <legend><img src="<?php echo get_theme_mod('simplon_prod_theme_custom_logo_setting',get_template_directory_uri().'/img/logo.png'); ?>" alt="logo" /></legend>
        <h1>- <?php bloginfo('name'); ?> -</h1>
        <h2><?php bloginfo('description'); ?></h2>
      </fieldset>
    </div>
  </div>
  <a href="#section_<?php echo $pages[0]->ID; ?>" class="more"><i class="fa fa-angle-down"></i></a>
</section>
<!-- /home -->
<!-- others_page -->
<?php
  foreach($pages as $page)
  {
    if(!$page->post_content) continue;
    $styles = page_style($page);
?>
<section id="section_<?php echo $page->ID; ?>"<?php if(strlen($styles)>0) { ?> style="<?php echo $styles; ?>" <?php } ?>>
  <h2><?php echo $page->post_title; ?></h2>
  <div class="entry"><?php echo $page->post_content; ?></div>
</section>
<?php
  }
?>
<!-- /others_page -->
<?php get_footer(); ?>