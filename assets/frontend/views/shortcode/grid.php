<div class="<?= PF_B_Manager::$action;?>-app-wrapper">
  <app :url.literal="'<?= admin_url( 'admin-ajax.php?'.http_build_query( array( 'nonce' => wp_create_nonce( PF_B_Manager::$gateway_nonce ), 'action' => PF_B_Manager::$action.PF_B_Manager::$gateway_action_suffix ) ) );?>'"></app>  
</div>