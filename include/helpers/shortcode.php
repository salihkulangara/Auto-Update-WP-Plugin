<?php
  class PF_B_Helpers_Shortcode
  {
    /**
     * data for shortcode localisation 
     * 
     * @var array
     *
     * @since 1.0.1.0
     * 
     */
    protected static $shortcode_localisation = array();

    /**
     * load shortcodes for VisualComposer
     *
     * @since 1.0.1.0
     * @since 1.0.1.5 added rules for checking existing VC
     * 
     */
    public static function init_vc()
    {
      if( !PF_B_Helpers_General::has_vc() )
        return false;
      
      vc_add_shortcode_param( PF_B_Manager::$action.'_heading' , function(){
        return '<p>'.__( 'This widget will display family profiles from your ParentFinder account. To change colors and manage info blocks go to Tools > ParentFinder Badge.', PF_B_Manager::$action ).'</p>';
      });


      self::load_vc( self::get_shortcode_localisation_param( 'grid' ), PF_B_Manager::$shortcodes['grid'], false );
      self::load_vc( self::get_shortcode_localisation_param( 'featured' ), PF_B_Manager::$shortcodes['featured'] );
    }

    /**
     * load shortcode in VisualComposer
     * 
     * @param  string  $name      
     * @param  string  $base      
     * @param  boolean $on_create 
     * 
     * @since 1.0.1.0
     * @since 1.0.1.5 removed rules for checking VC
     * 
     */
    public static function load_vc( $name, $base, $on_create = true )
    { 
      vc_map(
            array(
                'name'                     =>  $name,
                'base'                     =>  $base,
                'class'                    =>  'pf_b-vc-element',
                'icon'                     =>  PF_B_Manager::$assets_url.'dashboard/images/vc/'.$base.'.png',
                'show_settings_on_create'  =>  $on_create,
                'category'                 =>  __( 'ParentFinder', PF_B_Manager::$action ),
                'params'                   => self::vc_params( $base )
                  )
            );
    }

    /**
     * load params for VisualComposer shortcode by type
     * 
     * @param  string $type
     * 
     * @return array
     *
     * @since 1.0.1.0
     * 
     */
    protected static function vc_params( $type )
    {
      $params = array(
                    'pf-badge'          =>  array(
                                                  array(
                                                        "type"          =>  PF_B_Manager::$action.'_heading',
                                                        "holder"        =>  "div",
                                                        "class"         =>  "pf_b-hide-field",
                                                        "heading"       =>  "",
                                                        "value"         =>  "",
                                                        "param_name"    =>  "title",
                                                        ),
                                                ),
                    'pf-badge-featured' =>  array(
                                                  array(
                                                        "type"          =>  "dropdown",
                                                        "holder"        =>  "div",
                                                        "class"         =>  "pf_b-hide-field",
                                                        "heading"       =>  self::get_shortcode_localisation_param( 'type' ),
                                                        "value"         =>  self::tinymce_array_to_vc_array( self::get_shortcode_localisation_param( 'types' ) ),
                                                        "std"           =>  PF_B_Settings::$shortcode_settings['featured']['type'],
                                                        "save_always"   =>  true,
                                                        "param_name"    =>  "type",
                                                        ),
                                                  array(
                                                        "type"          =>  "textfield",
                                                        "holder"        =>  "div",
                                                        "class"         =>  "pf_b-hide-field",
                                                        "heading"       =>  self::get_shortcode_localisation_param( 'number_profiles' ),
                                                        "value"         =>  PF_B_Settings::$shortcode_settings['featured']['count'],
                                                        "description"   =>  self::get_shortcode_localisation_param( 'number_profiles_desc' ),
                                                        "save_always"   =>  true,
                                                        "param_name"    =>  "count",
                                                        ),
                                                  array(
                                                        "type"          =>  "dropdown",
                                                        "holder"        =>  "div",
                                                        "class"         =>  "pf_b-hide-field",
                                                        "heading"       =>  self::get_shortcode_localisation_param( 'grid_page' ),
                                                        "value"         =>  self::tinymce_array_to_vc_array( self::get_shortcode_localisation_param( 'grid_elements' ) ),
                                                        "save_always"   =>  true,
                                                        "param_name"    =>  "grid",
                                                        ),
                                                  array(
                                                        "type"          =>  "dropdown",
                                                        "holder"        =>  "div",
                                                        "class"         =>  "pf_b-hide-field",
                                                        "heading"       =>  self::get_shortcode_localisation_param( 'order' ),
                                                        "value"         =>  self::tinymce_array_to_vc_array( self::get_shortcode_localisation_param( 'order_elements' ) ),
                                                        "std"           =>  PF_B_Settings::$shortcode_settings['featured']['order'],
                                                        "save_always"   =>  true,
                                                        "param_name"    =>  "order",
                                                        ),
                                                  array(
                                                        "type"          =>  "dropdown",
                                                        "holder"        =>  "div",
                                                        "class"         =>  "pf_b-hide-field",
                                                        "heading"       =>  self::get_shortcode_localisation_param( 'style' ),
                                                        "value"         =>  self::tinymce_array_to_vc_array( self::get_shortcode_localisation_param( 'styles' ) ),
                                                        "std"           =>  PF_B_Settings::$shortcode_settings['featured']['style'],
                                                        "save_always"   =>  true,
                                                        "param_name"    =>  "style",
                                                        ),
                                                  ),
                    );

      return $params[ $type ];
    }

    /**
     * generate array for shortcode localisation
     * 
     * @return array
     *
     * @since 1.0.1.0
     * 
     */
    public static function get_shortcode_localisation()
    {
      self::init_shortcode_localisation();
      return self::$shortcode_localisation;
    }

    /**
     * init shortcode localisation
     * 
     * @since 1.0.1.0
     * 
     */
    protected static function init_shortcode_localisation()
    {
      if( empty( self::$shortcode_localisation ) )
        self::$shortcode_localisation = array(
                                                'action'                =>  PF_B_Manager::$action,
                                                'grid'                  =>  __( 'All Profiles', PF_B_Manager::$action ),
                                                'featured'              =>  __( 'Featured Profiles', PF_B_Manager::$action ),
                                                'number_profiles'       =>  __( 'Number of Profiles', PF_B_Manager::$action ),
                                                'number_profiles_desc'  =>  sprintf( __( 'Enter the total number of profiles to display. Default: %s.', PF_B_Manager::$action ), PF_B_Settings::$shortcode_settings['featured']['count'] ),
                                                'order'                 =>  __( 'Order', PF_B_Manager::$action ),
                                                'style'                 =>  __( 'Heading style', PF_B_Manager::$action ),
                                                'grid_page'             =>  __( 'Select page with profiles listing', PF_B_Manager::$action ),
                                                'type'                  =>  __( 'Select profile type to display', PF_B_Manager::$action ),
                                                'types'                 =>  json_encode( PF_B_Helpers_General::array_to_tinymce_select_options( array( 'placed' => __( 'Placed', PF_B_Manager::$action ), 'matched' => __( 'Matched', PF_B_Manager::$action ), 'both' => __( 'Both', PF_B_Manager::$action ) ), true ) ),
                                                'order_elements'        =>  json_encode( PF_B_Helpers_General::array_to_tinymce_select_options( PF_B_Manager::$options['display_order'] ) ),
                                                'grid_elements'         =>  json_encode( PF_B_Helpers_General::array_to_tinymce_select_options( PF_B_Helpers_General::get_elements(), true ) ),
                                                'styles'                =>  json_encode( PF_B_Helpers_General::array_to_tinymce_select_options( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) ),
                                                'default_values'        =>  PF_B_Settings::$shortcode_settings
                                              );
    }

    /**
     * get localisation param by name
     * 
     * @param  string $name
     * 
     * @return string||array
     *
     * @since 1.0.1.0
     * 
     */
    public static function get_shortcode_localisation_param( $name )
    {
      self::init_shortcode_localisation();
      return isset( self::$shortcode_localisation[ $name ] ) ? self::$shortcode_localisation[ $name ] : '';
    }

    /**
     * convert tinymce data to VisualComposer data
     * 
     * @param  string||array $data 
     * 
     * @return array
     *
     * @since 1.0.1.0
     * 
     */
    protected static function tinymce_array_to_vc_array( $data )
    {
      if( !is_array( $data ) )
        $data = json_decode( $data, true );

      $params = array();
      foreach( $data as $param )
        $params[ $param['text'] ] = $param['value'];

      return $params;
    }
  }