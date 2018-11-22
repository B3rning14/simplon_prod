<?php
/**
* Plugin Name: Citation at footer
* Plugin URI: http://prod.simplon.co/
* Version: 1.0.0
* Author: Leroy Kévin
* Author URI: http://www.b3rning14.fr/
* Description: Allows you to insert citations of customers at the footer (or sidebar) of every page of the site.
*/

$max_citations = 5; # -- NUMBER OF CITATION REGISTERED

/**
 * fcpi_register_settings
 * Register the settings of the plugin.
 */
function fcpi_register_settings()
{
  global $max_citations;

  register_setting(
    'footer-citation-settings',
    'citation_insert_footer_or_sidebar',
    ['type'=>'integer','default'=>0]
  );

  for ($i=1; $i<=$max_citations; $i++)
  {
    register_setting(
      'footer-citation-settings',
      'citation_'.$i,
      ['type'=>'string']
    );
  }

}

/**
 * fcpi_admin_panels
 * Create a new menu page for the plugin.
 */
function fcpi_admin_panels()
{

  global $max_citations;

  add_menu_page('Footer Citation - Options', 'Footer Citation', 'manage_options', __FILE__, function() use($max_citations)
  {
  ?>
    <form method="POST">
      <?php
        if(isset($_POST['submit']))
        {
          update_option('citation_insert_footer_or_sidebar',$_POST['citation_insert_footer_or_sidebar']);
          for ($i=1; $i<=$max_citations; $i++)
          {
            update_option('citation_'.$i,$_POST['citation_'.$i]);
          }
        }
      ?>
      <div class="wrap">
        <h2>Footer Citation - Options</h2>
        <?php settings_fields('footer-citation-settings'); ?>
        <table class="form-table">
          <tr>
            <th><label for="citation_insert_footer_or_sidebar">Footer or Sidebar ?</label></th>
            <td>
              <select name="citation_insert_footer_or_sidebar">
                <option value="0" <?php echo get_option('citation_insert_footer_or_sidebar') == 0 ? ' selected' : ''; ?>>Footer</option>
                <option value="1" <?php echo get_option('citation_insert_footer_or_sidebar') == 1 ? ' selected' : ''; ?>>Sidebar</option>
              </select>
            </td>
          </tr>
          <?php
            for ($i=1; $i<=$max_citations; $i++)
            {
          ?>
            <tr>
              <th>Citation n°<?php echo $i; ?></th>
              <td>
                <textarea rows="5" cols="30" name="citation_<?php echo $i; ?>"><?php echo get_option('citation_'.$i); ?></textarea>
              </td>
            </tr>
          <?php
            }
          ?>
        </table>
        <?php submit_button(); ?>
      </div>
    </form>
  <?php
  },'dashicons-admin-plugins');
}

/**
 * fcpi_frontend_code
 * Add Frontend code to insert in footer or sidebar.
 */
function fcpi_fontend_code()
{
  global $max_citations;
  $citations = array_filter(array_map(function($e) { return get_option('citation_'.$e); }, array_keys(array_fill(1,$max_citations,""))));

  echo '<div id="footercitations">
    <h3>Quelques clients satisfaits</h3>
    '.implode('',array_map(function($e) { return '<blockquote>'.$e.'</blockquote>'; },$citations)).'
    <ul>
      <li></li>
      '.implode('',array_fill(0,count($citations),'<li></li>')).'
      <li></li>
    </ul>
  </div>
  <script type="text/javascript">

    (function() {
   
      var citations = document.querySelectorAll("#footercitations blockquote");
      var cursors = document.querySelectorAll("#footercitations li:not(:first-child):not(:last-child)");
      var prev = document.querySelector("#footercitations li:first-child");
      var next = document.querySelector("#footercitations li:last-child");
      var firstIndex = 0;
      var lastIndex = cursors.length-1;
      var current_citation = firstIndex;

      prev.onclick = function()
      {
        current_citation = current_citation-1 >= firstIndex ? current_citation-1 : lastIndex;
        select_citation();
      };

      next.onclick = function()
      {
        current_citation = current_citation+1 <= lastIndex ? current_citation+1 : firstIndex;
        select_citation();
      };

      function select_citation()
      {
        console.log(current_citation);
        for (var citation of citations) citation.classList.remove("active");
        for (var cursor of cursors) cursor.classList.remove("active");
        cursors[current_citation].classList.add("active");
        citations[current_citation].classList.add("active");
      }

      select_citation();

    })();

  </script>';
}

/**
 * fcpi_css
 * Create stylesheet for the plugin
 */
function fcpi_css()
{
  echo '
  <style type="text/css">
    #footercitations
    {
      margin:15px auto;
      padding:5px;
      text-align:center;
    }

    #footercitations blockquote
    {
      display:none;
      width:100%;
      height:100%;
      font-style: italic;
      font-family: Georgia, "Times New Roman", serif;
      transition: height 1s;
    }

    #footercitations blockquote.active
    {
      display:block;
    }

    #footercitations ul
    {
      list-style-type: none;
      width: 100%;
      margin:5px 0;
    }

    #footercitations li
    {
      display:inline-block;
      width:30px;
      height:10px;
      user-select:none;
    }

    #footercitations li:not(:first-child):not(:last-child)
    {
      border-radius:50%;
      border:1px solid;
      width: 5px;
      height: 5px;
      margin: 0 1px;
      vertical-align: middle;
      transition: width .5s;
    }

    #footercitations li:not(:first-child):not(:last-child).active
    {
      width: 10px;
      height: 10px;
    }

    #footercitations li:first-child:after
    {
      content:"⇦";
    }

    #footercitations li:last-child:after
    {
      content:"⇨";
    }
  </style>
  ';
}

  // -- ADMIN HOOK (when page init and menu appear).
  if (is_admin())
  {
    add_action('admin_init','fcpi_register_settings');
    add_action('admin_menu','fcpi_admin_panels');
  }

  // -- CSS HOOK (when html head is created).
  add_action('wp_head','fcpi_css');
  
  // -- FOOTER OR SIDEBAR HOOK (depend of the value of citation_insert_footer_or_sidebar)
  // -- If footer : Add at the begin of the footer.
  // -- If sidebar : Add at the end of the very last widget displayed.
  if(boolval(get_option('citation_insert_footer_or_sidebar')))
    add_action(
      'wp_'.str_replace("-2","",end(wp_get_sidebars_widgets()['sidebar-1'])),
      'fcpi_fontend_code'
    );
  else
    add_action('wp_footer','fcpi_fontend_code');
?>