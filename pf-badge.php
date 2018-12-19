<?php
  /**
  * Plugin Name:        ParentFinder Badge
  * Description:        Display parent's profiles on your website using ParentFinder API. 
  * Plugin URI:         https://www.parentfinder.com/
  * Author:             Cairs
  * Author URI:         http://cairsolutions.com/
  * Version:            1.0.4.2
  **/

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/salihkulangara/Auto-Update-WP-Plugin/',
	__FILE__,
	'Auto-Update-WP-Plugin'
);

  add_action( 'plugins_loaded', array( 'PF_B_Manager', '_instance' ) );

  class PF_B_Manager
  {
    private static $_instance = NULL;

    /**
     * path to include folder
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $path;

    /**
     * path to include folder
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $assets_path;

    /**
     * full url to assets folder
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $assets_url;

    /**
     * version of plugin
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $version;

    /**
     * release number
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $release;

    /**
     * slug
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $action = 'pf_b';

    /**
     * key for generate nonce for ajax query get 
     * resized image for background of info blocks
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $info_block_nonce = 'ib';

    /**
     * name(suffix) of action for get resized image 
     * for background of info blocks
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $info_block_action_suffix = '_info_block_background';

    /**
     * key for generate nonce for ajax query get 
     * plugin settings on frontend
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $gateway_nonce = 'pf_b-settings';

    /**
     * name(suffix) of action for get plugin settings
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $gateway_action_suffix = '_api';

    /**
     * key for generate nonce for frontend styles
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $frontend_styles_nonce = 'frontend_styles';

    /**
     * name(suffix) of action for generate frontend styles
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    static $frontend_styles_suffix = '_frontend_styles';

    /**
     * array of plugin options from WordPress dashboard
     *
     * @var array
     * 
     * @since 1.0.0.0
     * 
     */
    static $options = array();

    /**
     * name of shortcode
     * 
     * @var string
     *
     * @since 1.0.0.0
     * @since 1.0.1.0 converted to array, added featured shortcode
     * 
     */
    static $shortcodes = array( 'grid' => 'pf-badge', 'featured' => 'pf-badge-featured' );

    private function __construct()
    {
      self::$path = dirname(__FILE__).'/include/';

      self::$assets_path = dirname(__FILE__).'/assets/';
      self::$assets_url = plugins_url( '', __FILE__ ).'/assets/';

      load_plugin_textdomain( self::$action, false, dirname( plugin_basename(__FILE__) ) . '/languages/' );

      include_once( self::$path.'helpers/general.php' );
      include_once( self::$path.'helpers/view.php' );
      include_once( self::$path.'helpers/api.php' );
      include_once( self::$path.'helpers/featured.php' );
      include_once( self::$path.'helpers/shortcode.php' );

      self::$version = self::_plugin_info( 'Version' );
      self::$release = self::_plugin_info( 'Release' );

      PF_B_Helpers_General::call_instance( 'PF_B_Settings', 'settings' );
      PF_B_Helpers_General::call_instance( 'PF_B_Loader', 'loader' );

      include_once( self::$path.'classes/gateway/gateway.php' );
      include_once( self::$path.'classes/gateway/routing.php' );
      include_once( self::$path.'classes/styles.php' );

      PF_B_Helpers_General::call_instance( 'PF_B_Ajax', 'ajax' );

      add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme' ) );
    }

    /**
     * list of actions after setup theme
     * 
     * @since 1.0.0.0
     * @since 1.0.1.0 added featured shortcode
     *                added to VisualComposer
     * 
     */
    static function after_setup_theme()
    {
      if( !PF_B_Gateway::check_api_key( self::$options['api_key'], self::$options['agency_id'] ) )
          return;

      add_image_size( PF_B_Manager::$action.'_'.self::$info_block_action_suffix.'_large', 408, 404, true );
      add_image_size( PF_B_Manager::$action.'_'.self::$info_block_action_suffix.'_small', 408, 202, true );

      add_shortcode( self::$shortcodes['grid'], function(){
        PF_B_Loader::frontend_js( 'grid' );
        PF_B_Loader::frontend_css( 'grid' );

        return PF_B_Helpers_View::load_to_variable( 'shortcode/grid', array(), 'php', false );
      });

      /**
       * @since 1.0.1.0
       */
      add_shortcode( self::$shortcodes['featured'], function( $atts ){
        $atts = shortcode_atts( array(
                                    'type'      => 'waiting',
                                    'count'     =>  4,
                                    'order'     =>  'random',
                                    'grid'      =>  0,
                                    'style'     =>  'h2'
                                    ), $atts 
                            );

        PF_B_Loader::frontend_js( 'featured' );
        PF_B_Loader::frontend_css( 'featured' );

        return PF_B_Helpers_View::load_to_variable( 'shortcode/featured/wrapper', array( 'profiles' => PF_B_Helpers_Featured::profiles( $atts ), 'atts' => $atts ), 'php', false );
      });

      /**
       * @since 1.0.1.0
       */
      PF_B_Helpers_Shortcode::init_vc();
    }

    /**
     * get information about plugin by type
     * 
     * @param  string $name Type of data field. 
     *                      Types https://codex.wordpress.org/File_Header
     * @return string       
     *
     * @since  1.0.0.0
     * 
     */
    protected static function _plugin_info( $name )
    { 
      /** WordPress Plugin Administration API */
      require_once(ABSPATH . 'wp-admin/includes/plugin.php');

      $data = get_plugin_data( PF_B_Manager::$path.'../pf-badge.php' );
      
      if( $name == 'Release' )
      {
        $d = explode( '.', $data['Version'] );
        return $d[ sizeof($d) - 1 ];
      }

      return $data[$name];
    }

    private function __clone() {}

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }
  }
