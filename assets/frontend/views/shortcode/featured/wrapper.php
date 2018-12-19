<div class="<?= PF_B_Manager::$action;?>-featured-wrapper <?= PF_B_Manager::$options['font_family'] != 'theme' ? PF_B_Manager::$action.'-featured-plugin-font' : '';?>">
  <?php 
    if( !empty( $profiles ) )
      foreach( $profiles as $profile )
        echo sprintf( PF_B_Helpers_Featured::get_profile_wrapper( $profile, $atts ), PF_B_Helpers_View::load_to_variable( 'shortcode/featured/content', array( 'profile' => $profile, 'atts' => $atts ), 'php', false ) );
  ?>
</div>