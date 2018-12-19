<div class="<?= PF_B_Manager::$action;?>-featured-image-wrapper">
  <img src="<?= $profile['img'];?>"/>
</div>
<div class="<?= PF_B_Manager::$action;?>-featured-profile-header" style="background-color: <?= $profile['label']['color'];?>;">
  <?= sprintf( '<div class="'.PF_B_Manager::$action.'-featured-profile-title"><'.$atts['style'].'>%s</'.$atts['style'].'></div>', PF_B_Helpers_Featured::get_names( $profile ) );?>
  <?= PF_B_Helpers_View::load_to_variable( 'shortcode/featured/badge', array( 'badge' => $profile['label'] ), 'php', false ); ?>
</div>