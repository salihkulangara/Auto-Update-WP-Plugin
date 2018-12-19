 <div class="wrap">
   <h1><?= esc_html( get_admin_page_title() ); ?></h1>
   <p><?= sprintf( __( 'To display profiles, copy and paste this shortcode to any page <code>%s</code>', PF_B_Manager::$action ), '['.PF_B_Manager::$shortcodes['grid'].'] or ['.PF_B_Manager::$shortcodes['featured'].']' );?></p>
   <form action="options.php" method="post" class="pf_b-form">
     <?php
      settings_fields( PF_B_Manager::$action );
      do_settings_sections( PF_B_Manager::$action );
      submit_button();
     ?>
   </form>
 </div>