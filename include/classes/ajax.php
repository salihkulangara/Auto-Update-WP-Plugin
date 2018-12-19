<?php
  class PF_B_Ajax
  { 
    protected static $_instance = NULL;

    private function __construct()
    {
      add_action( 'wp_ajax_'.PF_B_Manager::$action.PF_B_Manager::$info_block_action_suffix, array( __CLASS__, 'info_block_background' ) );
  
      add_action( 'wp_ajax_'.PF_B_Manager::$action.PF_B_Manager::$gateway_action_suffix, array( __CLASS__, 'gateway' ) );
      add_action( 'wp_ajax_nopriv_'.PF_B_Manager::$action.PF_B_Manager::$gateway_action_suffix, array( __CLASS__, 'gateway' ) );

      add_action( 'wp_ajax_'.PF_B_Manager::$action.PF_B_Manager::$frontend_styles_suffix, array( __CLASS__, 'styles' ) );
      add_action( 'wp_ajax_nopriv_'.PF_B_Manager::$action.PF_B_Manager::$frontend_styles_suffix, array( __CLASS__, 'styles' ) );
    }

    /**
     * create file with css rules
     * 
     * @return NULL
     *
     * @since 1.0.0.0
     * 
     */
    public static function styles()
    {
      if( !wp_verify_nonce( $_REQUEST['nonce'], PF_B_Manager::$frontend_styles_nonce ) )
        exit();

      header( 'Content-type: text/css' );

      echo PF_B_Styles::css();
       
      die();
    }

    /**
     * show site options, ads and parents as json
     * 
     * @return NULL
     *
     * @since 1.0.0.0
     * 
     */
    public static function gateway()
    {
      if( !wp_verify_nonce( $_REQUEST['nonce'], PF_B_Manager::$gateway_nonce ) )
        exit();

      if( !isset( $_REQUEST['request'] ) )
        exit();

      PF_B_Gateway_Routing::_instance( htmlspecialchars( $_REQUEST['request'] ) );

      die();
    }

    /**
     * return url for resized image
     *
     * @param string $nonce  key for access
     * @param string $size   image size small||large
     * @param int    $image  ID of image
     * 
     * @return string URL
     *
     * @since 1.0.0.0
     * 
     */
    public static function info_block_background()
    {
      if( !wp_verify_nonce( $_REQUEST['nonce'], PF_B_Manager::$info_block_nonce ) )
        exit();

      if( !isset($_REQUEST['size']) || !in_array( $_REQUEST['size'] , array( 'small', 'large' ) ) )
        exit();

      if( !isset($_REQUEST['image']) )
        exit();

      $id = (int)$_REQUEST['image'];
      $size = htmlspecialchars( $_REQUEST['size'] );

      echo PF_B_Helpers_View::get_info_block_background_image_url( $id, $size );

      die();
    }

    private function __clone() {}

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }
  }