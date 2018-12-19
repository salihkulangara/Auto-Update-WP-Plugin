<?php
  class PF_B_Loader
  { 
    protected static $_instance = NULL;
    
    /**
     * init load actions
     *
     * @since 1.0.0.0
     */
    private function __construct()
    {
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ) );

      /**
       * @since 1.0.1.0 init tinymce button and scripts
       */
      add_action( 'admin_head', array( $this, 'admin_tinymce' ) );
    }

    /**
     * load PF button settings for tinymce
     * 
     * @since 1.0.1.0
     * 
     */
    public static function admin_tinymce()
    {
      if( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )
            return;
        
      if( 'true' == get_user_option( 'rich_editing' ) ) 
      {
        /**
         * add js for tinymce
         *
         * @param array $plugins
         *
         * @return array $plugins
         *
         * @since 1.0.1.0
         * 
         */
        add_filter( 'mce_external_plugins', function( $plugins ){
          $plugins[ PF_B_Manager::$action.'_shortcodes' ] = PF_B_Manager::$assets_url .'dashboard/js/tinymce.js';
          return $plugins;
        });

        /**
         * add button to tinymce
         *
         * @param array $buttons
         *
         * @return array $buttons
         * 
         * @since 1.0.1.0
         * 
         */
        add_filter( 'mce_buttons', function( $buttons ){
          array_push( $buttons, PF_B_Manager::$action.'_shortcodes' );
          return $buttons;
        });

      }
    }

    /**
     * Function that will add javascript for DashBoard
     *
     * @param string $hook identificator of current screen
     * 
     * @since 1.0.0.0
     * @since 1.0.1.0 updated loading dashboard.js 
     *                moved translated to dashboard.js
     * 
     */
    public static function admin_js( $hook )
    {
      /**
       * @since 1.0.1.0
       */
      $dashboard_deps = array( 'jquery' );

      wp_enqueue_script( 'jquery' );

      if( $hook == 'tools_page_pf_b' )
      {
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_media();

        wp_enqueue_script( PF_B_Manager::$action.'_defaultValue', PF_B_Manager::$assets_url.'dashboard/js/jquery.defaultValue.js', array( 'jquery' ), 'v0.2.0', true  );
        wp_enqueue_script( PF_B_Manager::$action.'_jsrender', PF_B_Manager::$assets_url.'dashboard/js/jsrender.min.js', array( 'jquery' ), 'v0.9.85', true  );
        wp_enqueue_script( PF_B_Manager::$action.'_info-blocks', PF_B_Manager::$assets_url.'dashboard/js/info-blocks.js', array( 'jquery', 'jquery-ui-sortable', PF_B_Manager::$action.'_jsrender', PF_B_Manager::$action.'_defaultValue' ), PF_B_Manager::$version, true );
      
        $dashboard_deps = array_merge( $dashboard_deps, array( PF_B_Manager::$action.'_info-blocks', 'wp-color-picker' ) );
      }

      /**
       * @since 1.0.1.0
       */
      wp_register_script( PF_B_Manager::$action.'_dashboard-js', PF_B_Manager::$assets_url.'dashboard/js/dashboard.js', $dashboard_deps, PF_B_Manager::$version, true  );
      unset( $dashboard_deps );

      $translations = array(
        'info_blocks' => array(
                                'select_image'          => __( 'Select image', PF_B_Manager::$action ),
                                'insert_image'          => __( 'Insert', PF_B_Manager::$action ),
                                'button_add_image'      => __( 'Upload', PF_B_Manager::$action ),
                                'button_remove_image'   => __( 'Remove', PF_B_Manager::$action ),
                              ),
        'tinymce'     => PF_B_Helpers_Shortcode::get_shortcode_localisation()
      );
      wp_localize_script( PF_B_Manager::$action.'_dashboard-js', PF_B_Manager::$action.'_translations', $translations );

      wp_enqueue_script( PF_B_Manager::$action.'_dashboard-js' );
    }

    /**
     * Function that will add css for DashBoard
     *
     * @param string $hook identificator of current screen
     * 
     * @since 1.0.0.0
     * @since 1.0.1.0 updated loading dashboard.css
     * 
     */
    public static function admin_css( $hook )
    { 
      /**
       * @since 1.0.1.0
       */
      $dashboard_deps = array();

      if( $hook == 'tools_page_pf_b' )
      {
        wp_enqueue_style( 'wp-color-picker' ); 
        wp_enqueue_style( PF_B_Manager::$action.'_info-blocks', PF_B_Manager::$assets_url.'dashboard/css/info-blocks.css', array(), PF_B_Manager::$version );
        
        $dashboard_deps = array_merge( $dashboard_deps, array( PF_B_Manager::$action.'_info-blocks' ) );
      }

      wp_enqueue_style( PF_B_Manager::$action.'_dashboard-css', PF_B_Manager::$assets_url.'dashboard/css/dashboard.css', $dashboard_deps, PF_B_Manager::$version );
      unset( $dashboard_deps );
    }

    /**
     * Function that will add js for FrontEnd
     *
     * @param string $type type of loading grid||featured
     * 
     * @since 1.0.0.0
     * @since 1.0.1.0 added type
     * @since 1.0.1.6 added promise
     * 
     */
    public static function frontend_js( $type )
    {
      wp_enqueue_style( 'jquery' ); 

      if( $type == 'grid' )
      {
        wp_enqueue_script( PF_B_Manager::$action.'_frontend_promise', 'https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( PF_B_Manager::$action.'_frontend_fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js', array( 'jquery' ), '3.0.47', true );
        wp_enqueue_script( PF_B_Manager::$action.'_frontend_intro', 'https://cdn.jsdelivr.net/intro.js/2.5.0/intro.min.js', array( 'jquery' ), '2.5.0', true );

        wp_enqueue_script( PF_B_Manager::$action.'_frontend_vendor', PF_B_Manager::$assets_url.'frontend/js/vendor.js', array( PF_B_Manager::$action.'_frontend_fancybox', PF_B_Manager::$action.'_frontend_intro', PF_B_Manager::$action.'_frontend_promise' ), PF_B_Manager::$version, true );
        wp_enqueue_script( PF_B_Manager::$action.'_frontend_build', PF_B_Manager::$assets_url.'frontend/js/build.js', array( PF_B_Manager::$action.'_frontend_vendor' ), PF_B_Manager::$version, true );
      }
    }

    /**
     * Function that will add css for FrontEnd
     *
     * @param string $type type of loading grid||featured
     * 
     * @since 1.0.0.0
     * @since 1.0.1.0 added type
     * 
     */
    public static function frontend_css( $type )
    {
      $deps = array();
      
      /**
       * @since 1.0.1.0 updates for including font
       */
      if( PF_B_Manager::$options['font_family'] != 'theme' ):
        wp_enqueue_style( PF_B_Manager::$action.'_frontend_font', PF_B_Manager::$assets_url.'frontend/css/fonts.css' , array(), PF_B_Manager::$version );
        $deps[] = PF_B_Manager::$action.'_frontend_font';
      endif;

      if( $type == 'grid' )
      {
        wp_enqueue_style( PF_B_Manager::$action.'_frontend_fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css', array(), '3.0.47' );
        wp_enqueue_style( PF_B_Manager::$action.'_frontend_intro', 'https://cdn.jsdelivr.net/intro.js/2.5.0/introjs.min.css', array(), '2.5.0' );

        $deps = array_merge( $deps, array( PF_B_Manager::$action.'_frontend_fancybox', PF_B_Manager::$action.'_frontend_intro' ) );

        wp_enqueue_style( PF_B_Manager::$action.'_frontend_theme', admin_url( 'admin-ajax.php?'.http_build_query( array( 'nonce' => wp_create_nonce( PF_B_Manager::$frontend_styles_nonce ), 'action' => PF_B_Manager::$action.PF_B_Manager::$frontend_styles_suffix ) ) ) , $deps, PF_B_Manager::$version );
      }

      /**
       * @since 1.0.1.0
       */
      if( $type == 'featured' )
        wp_enqueue_style( PF_B_Manager::$action.'_frontend_featured', PF_B_Manager::$assets_url.'frontend/css/featured.css' , $deps, PF_B_Manager::$version );
    }

    private function __clone() {}

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }
  }