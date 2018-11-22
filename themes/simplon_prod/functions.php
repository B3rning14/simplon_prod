<?php

  /*
   *  Author: Leroy Kévin <hello@b3rning14.fr>
   *  URL: http://www.b3rning14.fr
   *  Custom functions, support, custom post types and more.
   */

  /**
   * theme_styles
   * Register and enqueue style and font.
   */
  function theme_styles()
  {
    //https://use.fontawesome.com/releases/v5.5.0/css/all.css
    wp_register_style('fontawesome', get_template_directory_uri().'/font/fontawesome/all.css', [], '1.0', 'all');
    wp_enqueue_style('fontawesome');

    //https://fonts.googleapis.com/css?family=Montserrat
    wp_register_style('montserrat', get_template_directory_uri().'/font/montserrat/stylesheet.css', [], '1.0', 'all');
    wp_enqueue_style('montserrat');

    wp_register_style('theme-style', get_template_directory_uri().'/style.css', [], '1.0', 'all');
    wp_enqueue_style('theme-style');
  }

  /**
   * theme_scripts
   * Register and enqueue scripts.
   * Only on frontend.
   */
  function theme_scripts()
  {
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin())
    {
      wp_register_script('theme-script', get_template_directory_uri().'/js/scripts.js', ['jquery'], '1.0.0');
      wp_enqueue_script('theme-script');
    }
  }

  /**
   * theme_register
   * Add new options for customize the theme.
   */
  function theme_customize($wp_customize)
  {

    $wp_customize->add_section(
      'simplon_prod_theme_custom', 
      [
        'title'      => 'Theme Customization',
        'transport'  => 'postMessage'
      ]
    );

    $wp_customize->add_setting(
      'simplon_prod_theme_custom_background_image_setting',
      [
        'default'   => get_template_directory_uri() . '/img/welcome.jpg',
        'transport'   => 'postMessage'
      ]
    );

    $wp_customize->add_control(
      new WP_Customize_Image_Control(
        $wp_customize,
        'simplon_prod_theme_custom_background_image_control',
        [
          'settings'    => 'simplon_prod_theme_custom_background_image_setting',
          'section'   => 'simplon_prod_theme_custom',
          'label'     => 'Home Background Image',
          'description' => 'Add custom background image to home page'
        ]
      )
    );

  }

  /**
   * theme_nav_register
   * Register navigation menu(s).
   */
  function theme_nav_register()
  {
    register_nav_menu('header-menu',__( 'Menu Principal', 'Primary Menu'));
  }

  /**
   * theme_nav
   * Create primary menu.
   */
  function theme_nav()
  {
    if(is_home())
    {
      echo '<a class="menu-hamburger" href="javascript:void(0)"><i class="fa fa-bars"></i></a>
      <div class="menu-wrapper">';
      wp_nav_menu([
        'theme_location' => 'header-menu',
        'depth' => 1,
        'container' => 'div'
      ]);
      echo '</div>';
    }
    else
    {
      echo '<a href="'.get_home_url().'"><i class="fa fa-arrow-left"></i></a>';
    }
  }

  /**
   * page_style
   * Create primary menu.
   * @param $page The current page.
   * @return The style to apply at page.
   */
  function page_style(&$page)
  {
    $styles = [];
    $includes = [];
    
    // -- ADD Background Color (Use: <!--BG-COLOR-{Color Name or Hex}-->).
    page_style_single(
      'BG-COLOR',
      'background-color',
      $page,
      $styles
    );
    
    // -- ADD Background Image (Use : <!--BG-IMG--><img{1}><!--/BG-IMG-->).
    page_style_between(
      'BG-IMG',
      'background-image',
      $page,
      $styles,
      function(&$e)
      {
        $dom = new DOMDocument();
        @$dom->loadHTML($e);
        $e = "url('".$dom->getElementsByTagName('img')->item(0)->getAttribute('src')."')";
      }
    );

    // -- ADD Slideshow (Use : <!--SLIDESHOW--><img{1,}><!--/SLIDESHOW-->).
    page_style_between(
      'SLIDESHOW',
      'slideshow',
      $page,
      $includes,
      function(&$e)
      {
        
        $dom = new DOMDocument();
        @$dom->loadHTML($e);
        $imgs = iterator_to_array($dom->getElementsByTagName('img'));
        $imgc = count($imgs);

        if($imgc==0)
        {
          $e = '<div><i class="fa fa-exclamation-triangle"></i>Warning!&nbsp;No image in slideshow</div>';
        }
        else
        {
          $imgs = implode("",array_map(function($e)
          {
            //class="lazy" data-
            return '<img src="'.$e->getAttribute('src').'" />';
          }, $imgs));
          $e = '
          <div class="slideshow">
            <nav>
              <a class="left">
                <i class="fa fa-arrow-left"></i>
              </a>
              <a class="right">
                <i class="fa fa-arrow-right"></i>
              </a>
              <ul>'.implode("",array_fill(0, $imgc, '<li></li>')).'</ul>
            </nav>
            <div class="slideshow-images">'.$imgs.'</div>
          </div>
          ';
        }
      },
      true
    );

    // -- COMPUTE STYLE RULES.
    array_walk(
      $styles,
      function(&$a,$b)
      {
        $a = "$b:$a";
      }
    );

    // -- REPLACE COMPUTE CONTENT IN PAGE CONTENT.
    array_walk(
      $includes, 
      function($a) use(&$page)
      {
        $page->post_content = str_replace($a[0], $a[1], $page->post_content);
      }
    );

    // -- RETURN THE STYLE TO APPLY AT PAGE.
    return implode(';',$styles);
  }

  /**
   * page_style_single
   * Search regex in page content and get or replace this with computed content.
   * The format of regex is : <!--{key}-(.*)-->
   * @param $search The value to search.
   * @param $rule The style rule.
   * @param $page The current page.
   * @param $return The value to return.
   * @param $callback A callback to compute content.
   * @param $keep_original If true, keep original content and return this with computed content as an array. elsewise the original content is delete.
   */
  function page_style_single($search,$rule,&$page,&$return,$callback=null,$keep_original=false)
  {
    page_style_regex(
      sprintf('/<!--%s-(.*)-->/m',$search),
      $rule,
      $page,
      $return,
      $callback,
      $keep_original
    );
  }

  /**
   * page_style_between
   * Search regex in page content and get or replace this with computed content.
   * The format of regex is : <!--{key}-->(.*)<!--/{key}-->
   * @param $search The value to search.
   * @param $rule The style rule.
   * @param $page The current page.
   * @param $return The value to return.
   * @param $callback A callback to compute content.
   * @param $keep_original If true, keep original content and return this with computed content as an array. elsewise the original content is delete.
   */
  function page_style_between($search,$rule,&$page,&$return,$callback=null,$keep_original=false)
  {
    page_style_regex(
      sprintf('/<!--%s-->([\s\S]*?)<!--\/%s-->/m',$search,$search),
      $rule,
      $page,
      $return,
      $callback,
      $keep_original
    );
  }

  /**
   * page_style_regex
   * Search regex in page content and get or replace this with computed content.
   * @param $regex The regex.
   * @param $rule The style rule.
   * @param $page The current page.
   * @param $return The value to return.
   * @param $callback A callback to compute content.
   * @param $keep_original If true, keep original content and return this with computed content as an array. elsewise the original content is delete.
   */
  function page_style_regex($regex,$rule,&$page,&$return,$callback=null,$keep_original=false)
  {
    if(preg_match($regex, $page->post_content, $result)==1)
    {
      
      if(!is_null($callback))
        $callback($result[1]);

      $return[$rule] = $keep_original ? $result : $result[1];

      if(!$keep_original)
        $page->post_content = str_replace($result[0], "", $page->post_content);
    }
  }

  /**
   * remove_admin_login_header
   * Remove Admin Login Header in Frontpage.
   */
  function remove_admin_login_header() 
  {
    remove_action('wp_head', '_admin_bar_bump_cb');
  }

  // -- HOOKS
  add_action('init','theme_scripts');
  add_action('wp_enqueue_scripts','theme_styles');
  add_action('get_header','remove_admin_login_header');
  add_action('customize_register','theme_customize');
  add_action('after_setup_theme','theme_nav_register');
?>