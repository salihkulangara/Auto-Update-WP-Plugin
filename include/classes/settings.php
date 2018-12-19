<?php
  class PF_B_Settings
  { 
    protected static $_instance = NULL;

    /**
     * name of capability for access to settings page
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    protected static $capability = 'manage_options';

    /**
     * suffix for name of options
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    public static $options_suffix = '_options';

    /**
     * list of shortcodes default settings
     * 
     * @var array
     *
     * @since 1.0.1.0
     * 
     */
    public static $shortcode_settings = array(
                                            'featured'  =>  array(
                                                                    'type'  =>  'waiting',
                                                                    'count'  =>  '4',
                                                                    'order'  =>  'random',
                                                                    'style'  =>  'h2',
                                                                  )
                                              );

    private function __construct()
    {
      self::set_current_options();

      add_action( 'admin_init', array( __CLASS__, 'settings_init' ) );
      add_action( 'admin_menu', array( __CLASS__, 'init_page' ) );
    }

    private function __clone() {}

    /**
     * load options from dashboard and catenation with default options
     *
     * @return  array
     *
     * @since 1.0.0.0
     * 
     */
    protected static function set_current_options()
    {
      $db_options = self::get_options();

      if( !empty($db_options) )
        foreach( array( 'matched_badge' => array( 'badge', 'color' ), 'placed_badge' => array( 'badge', 'color' ), 'in_progress_badge' => array( 'badge', 'color' ), 'waiting_badge' => array( 'badge', 'color' ), 'primary_color', 'secondary_color' ) as $group_key => $group )
          if( is_array( $group ) )
          {
            foreach( $group as $key )
              if( empty( $db_options[ $group_key ][ $key ] ) )
                unset( $db_options[ $group_key ][ $key ] );
            
            if( empty( $db_options[ $group_key ] ) )
                unset( $db_options[ $group_key ] );
          }else
            if( empty( $db_options[ $group ] ) )
                unset( $db_options[ $group ] );

      PF_B_Manager::$options = self::default_options();
      
      if( !empty( $db_options ) )
        foreach( PF_B_Manager::$options as $key => $option )
          if( in_array( $key, array( 'matched_badge', 'placed_badge', 'in_progress_badge', 'waiting_badge', 'info_blocks' ) ) )
          {
            foreach( $option as $sub_key => $sub_option )
              if( isset( $db_options[ $key ][ $sub_key ] ) )
                PF_B_Manager::$options[ $key ][ $sub_key ] = $db_options[ $key ][ $sub_key ];
          }else
            if( isset( $db_options[ $key ] ) )
              PF_B_Manager::$options[ $key ] = $db_options[ $key ];
      
      PF_B_Manager::$options['info_blocks']['show'] = !isset( PF_B_Manager::$options['info_blocks']['show'] ) ? 0 : PF_B_Manager::$options['info_blocks']['show'];
      PF_B_Manager::$options['info_blocks']['data'] = json_decode( urldecode( PF_B_Manager::$options['info_blocks']['data'] ) );
    }

    /**
     * get options
     * 
     * @return array
     *
     * @since 1.0.0.0
     * 
     */
    protected static function get_options()
    {
      return get_option( PF_B_Manager::$action.self::$options_suffix );
    }

    /**
     * get default settings for dashboard
     * 
     * @param  string $field field name
     * 
     * @return array||string
     *
     * @since 1.0.0.0
     * 
     */
    protected static function default_options( $field = '' )
    {
      $data = array(
                                      'elements_per_page'   =>  '12',
                                      'primary_color'       =>  '#bbdc9b',
                                      'secondary_color'     =>  '#f5b3d1',
                                      'font_family'         =>  'theme',
                                      'agency_id'           =>  '',
                                      'disable_in_progress' =>  0,
                                      'api_key'             =>  '',
                                      'show_religion' => 'show',
                                      'show_waiting' => 'show',
                                      'show_education' => 'show',
                                      'show_family_ethnicity'=> 'show',
                                      'show_family_age' => 'hide',
                                      'show_gender' => 'hide',
                                      'show_state' => 'hide',
                                      'show_childrens_age' => 'hide',
                                      'show_no_of_children' => 'hide',
                                      'show_child_ethnicity' => 'show',
                                      'show_child_age' => 'hide',
                                      'show_adoption_type' => 'hide',
                                      'matched_badge'       =>  array(
                                                                      'badge'       =>  __( 'Matched', PF_B_Manager::$action ),
                                                                      'color'       =>  '#F196BF',
                                                                      ),
                                      'placed_badge'        =>  array(
                                                                      'badge'       =>  __( 'Placed', PF_B_Manager::$action ),
                                                                      'color'       =>  '#7EBE47',
                                                                      ),
                                      'in_progress_badge'   =>  array(
                                                                      'badge'       =>  __( 'In Progress', PF_B_Manager::$action ),
                                                                      'color'       =>  '#828282',
                                                                      ),
                                      'waiting_badge'   =>  array(
                                                                      'badge'       =>  __( 'Waiting', PF_B_Manager::$action ),
                                                                      'color'       =>  '#7b2121',
                                                                      ),
                                      'display_order'       =>  array(
                                                                        'random',
                                                                        'oldest',
                                                                        'youngest',
                                                                        'first_name'
                                                                      ),
                                      'sorting_options'     =>  array(
                                                                        'favorite',
                                                                        'kidsInFamily',
                                                                        'religion',
                                                                        'location',
                                                                        'waiting',
                                                                        'avatarLabel',
                                                                        'sort_by'
                                                                      ),
                                      'info_blocks'         =>  array(
                                                                        'show'      =>  0,
                                                                        'position'  =>  7,
                                                                        'data'      =>  '[]'
                                                                      )
                                    );

      return empty( $field ) ? $data : $data[ $field ];
    }

    /**
     * init settings page
     * 
     * @since 1.0.0.0
     * 
     */
    public static function init_page()
    {
      add_submenu_page(
                          'tools.php',
                          __( 'ParentFinder Badge', PF_B_Manager::$action ),
                          __( 'ParentFinder Badge', PF_B_Manager::$action ),
                          self::$capability,
                          PF_B_Manager::$action,
                          array( __CLASS__, 'render_page' )
                      );
    }

    /**
     * generate HTML code of settings page
     * 
     * @since 1.0.0.0
     * 
     */
    public static function render_page() 
    {
      if ( !current_user_can( self::$capability ) )
        return;
 
      if ( isset( $_GET['settings-updated'] ) )
        add_settings_error( 
                            PF_B_Manager::$action.'_messages', 
                            PF_B_Manager::$action.'_message', 
                            __( 'Settings Saved', PF_B_Manager::$action ), 
                            'updated' 
                          );
 
      settings_errors( PF_B_Manager::$action.'_messages' );
    
      PF_B_Helpers_View::get_template_part( 'settings/page' );
    }

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }

    /**
     * init settings fields
     * 
     * @since 1.0.0.0
     * 
     */
    public static function settings_init()
    {
      register_setting( PF_B_Manager::$action, PF_B_Manager::$action.self::$options_suffix, array( __CLASS__, 'validate_options' ) );

      /* INIT SECTIONS */

      add_settings_section(
                            PF_B_Manager::$action.'_section_general',
                            '',
                            array( __CLASS__, 'render_section' ),
                            PF_B_Manager::$action
                          );

    add_settings_section(
                    PF_B_Manager::$action.'_section_details_start',
                    __( '<hr />', PF_B_Manager::$action ),
                    array( __CLASS__, 'render_section' ),
                    PF_B_Manager::$action
                  );
      
      add_settings_section(
                            PF_B_Manager::$action.'_section_details',
                            __( '<span class="details-head">Display Options</span>', PF_B_Manager::$action ),
                            array( __CLASS__, 'render_section' ),
                            PF_B_Manager::$action
                          );
      
     
      add_settings_section(
                            PF_B_Manager::$action.'_section_family_details',
                            __( 'Family Options', PF_B_Manager::$action ),
                            array( __CLASS__, 'render_section' ),
                            PF_B_Manager::$action
                          );

      add_settings_section(
                            PF_B_Manager::$action.'_section_child_preference',
                            __( 'Child Options', PF_B_Manager::$action ),
                            array( __CLASS__, 'render_section' ),
                            PF_B_Manager::$action
                          );
      
     add_settings_section(
                            PF_B_Manager::$action.'_section_details_end',
                            __( '<hr />', PF_B_Manager::$action ),
                            array( __CLASS__, 'render_section' ),
                            PF_B_Manager::$action
                          );
            
      add_settings_section(
                            PF_B_Manager::$action.'_section_display',
                            __( 'Display', PF_B_Manager::$action ),
                            array( __CLASS__, 'render_section' ),
                            PF_B_Manager::$action
                          );

      add_settings_section(
                            PF_B_Manager::$action.'_section_customisation',
                            __( 'Customisation', PF_B_Manager::$action ),
                            array( __CLASS__, 'render_section' ),
                            PF_B_Manager::$action
                          );

      /* END INIT SECTIONS */

      /* INIT FIELDS */

      add_settings_field(
                          PF_B_Manager::$action.'_api_key',
                          __( 'API Key', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_general',
                          array(
                                  'name'  =>  'api_key',
                                  'type'  =>  'text',
                                )
                        );

      add_settings_field(
                          PF_B_Manager::$action.'_agency_id',
                          __( 'Agency ID', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_general',
                          array(
                                  'name'  =>  'agency_id',
                                  'type'  =>  'text',
                                )
                        );

      /**Rasheed modifications 7-9-2018**/
      add_settings_field(
                          PF_B_Manager::$action.'_show_religion',
                          __( 'Religion', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_family_details',
                          array(
                                  'name'      =>  'show_religion',
                                  'type'      =>  'radio',
                                  'data'      =>  array(
                                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                                    )
                                )
                        );

      add_settings_field(
                    PF_B_Manager::$action.'_show_waiting',
                    __( 'Waiting', PF_B_Manager::$action ),
                    array( __CLASS__, 'field' ),
                    PF_B_Manager::$action,
                    PF_B_Manager::$action.'_section_family_details',
                    array(
                            'name'      =>  'show_waiting',
                            'type'      =>  'radio',
                            'data'      =>  array(
                                                'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                                'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                              )
                          )
                  );


      add_settings_field(
          PF_B_Manager::$action.'_show_education',
          __( 'Education', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_family_details',
          array(
                  'name'      =>  'show_education',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );     

      add_settings_field(
          PF_B_Manager::$action.'_show_family_ethnicity',
          __( 'Ethnicity', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_family_details',
          array(
                  'name'      =>  'show_family_ethnicity',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );    
            
            
      add_settings_field(
          PF_B_Manager::$action.'_show_family_age',
          __( ' Age', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_family_details',
          array(
                  'name'      =>  'show_family_age',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );   
      
      add_settings_field(
          PF_B_Manager::$action.'_show_gender',
          __( ' Gender', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_family_details',
          array(
                  'name'      =>  'show_gender',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );               
        
      add_settings_field(
          PF_B_Manager::$action.'_show_state',
          __( 'Country / State', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_family_details',
          array(
                  'name'      =>  'show_state',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );   

      add_settings_field(
          PF_B_Manager::$action.'_show_childrens_age',
          __( 'Children\'s age', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_family_details',
          array(
                  'name'      =>  'show_childrens_age',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );                         


      add_settings_field(
          PF_B_Manager::$action.'_show_no_of_children',
          __( 'Number of children', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_family_details',
          array(
                  'name'      =>  'show_no_of_children',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        ); 
      
      
            add_settings_field(
          PF_B_Manager::$action.'_show_child_ethnicity',
          __( 'Ethnicity', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_child_preference',
          array(
                  'name'      =>  'show_child_ethnicity',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );    
      
      add_settings_field(

          PF_B_Manager::$action.'_show_child_age',
          __( 'Age', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_child_preference',
          array(
                  'name'      =>  'show_child_age',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );    
      
      add_settings_field(
          PF_B_Manager::$action.'_show_adoption_type',
          __( 'Adoption type', PF_B_Manager::$action ),
          array( __CLASS__, 'field' ),
          PF_B_Manager::$action,
          PF_B_Manager::$action.'_section_child_preference',
          array(
                  'name'      =>  'show_adoption_type',
                  'type'      =>  'radio',
                  'data'      =>  array(
                                      'show'      =>  __( 'Show', PF_B_Manager::$action ),
                                      'hide'      =>  __( 'Hide', PF_B_Manager::$action )
                                    )
                )
        );    

      /**Rasheed modifications 7-9-2018**/
      
      
      add_settings_field(
                          PF_B_Manager::$action.'_elements_per_page',
                          __( 'Number of families to display', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_display',
                          array(
                                  'name'  =>  'elements_per_page',
                                  'type'  =>  'text',
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_display_order',
                          __( 'Display Order', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_display',
                          array(
                                  'name'      =>  'display_order',
                                  'type'      =>  'select',
                                  'data'      =>  array(
                                                      'random'      =>  __( 'Random', PF_B_Manager::$action ),
                                                      'oldest'      =>  __( 'Oldest waiting', PF_B_Manager::$action ),
                                                      'youngest'    =>  __( 'Youngest waiting', PF_B_Manager::$action ),
                                                      'first_name'  =>  __( 'First Name', PF_B_Manager::$action )
                                                    ),
                                  'multiple'  =>  'multiple'
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_sorting_options',
                          __( 'Sorting Options', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_display',
                          array(
                                  'name'      =>  'sorting_options',
                                  'type'      =>  'select',
                                  'data'      =>  array(
                                                      'favorite'            =>  __( 'Profiles I like', PF_B_Manager::$action ),
                                                      'kidsInFamily'        =>  __( 'Kids in family', PF_B_Manager::$action ),
                                                      'religion'            =>  __( 'Religion', PF_B_Manager::$action ),
                                                      'location'            =>  __( 'Country/State', PF_B_Manager::$action ),
                                                      'waiting'             =>  __( 'Waiting', PF_B_Manager::$action ),
                                                      'avatarLabel'         =>  __( 'Status', PF_B_Manager::$action ),
                                                      'sort_by'             =>  __( 'Sort by', PF_B_Manager::$action ),
                                                    ),
                                  'multiple'  =>  'multiple'
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_disable_in_progress',
                          __( 'Disable In Progress Profiles on site?', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_display',
                          array(
                                  'name'      =>  'disable_in_progress',
                                  'type'      =>  'select',
                                  'data'      =>  array(
                                                      1      =>  __( 'Yes', PF_B_Manager::$action ),
                                                      0      =>  __( 'No', PF_B_Manager::$action ),
                                                    ),
                                )
                        );

      add_settings_field(
                          PF_B_Manager::$action.'_primary_color',
                          __( 'Primary Color', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'  =>  'primary_color',
                                  'type'  =>  'text',
                                  'class' =>  PF_B_Manager::$action.'_colorpicker'
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_secondary_color',
                          __( 'Secondary Color', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'  =>  'secondary_color',
                                  'type'  =>  'text',
                                  'class' =>  PF_B_Manager::$action.'_colorpicker'
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_font_family',
                          __( 'Font family', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'  =>  'font_family',
                                  'type'  =>  'select',
                                  'data'      =>  array(
                                                      'theme'          =>  __( 'From current theme', PF_B_Manager::$action ),
                                                      'plugin'         =>  __( 'From plugin', PF_B_Manager::$action ),
                                                    ),
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_matched_badge',
                          __( 'Matched Badge', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'        =>  'matched_badge',
                                  'type'        =>  'badge',
                                  'description' =>  sprintf( __( 'If left empty will show %s', PF_B_Manager::$action ), __( 'MATCHED', PF_B_Manager::$action ) )
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_placed_badge',
                          __( 'Placed Badge', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'        =>  'placed_badge',
                                  'type'        =>  'badge',
                                  'description' =>  sprintf( __( 'If left empty will show %s', PF_B_Manager::$action ), __( 'Placed', PF_B_Manager::$action ) )
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_in_progress_badge',
                          __( 'In Progress Badge', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'        =>  'in_progress_badge',
                                  'type'        =>  'badge',
                                  'description' =>  sprintf( __( 'If left empty will show %s', PF_B_Manager::$action ), __( 'In Progress', PF_B_Manager::$action ) )
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_waiting_badge',
                          __( 'Waiting Badge', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'        =>  'waiting_badge',
                                  'type'        =>  'badge',
                                  'description' =>  sprintf( __( 'If left empty will show %s', PF_B_Manager::$action ), __( 'Waiting', PF_B_Manager::$action ) )
                                )
                        );
      add_settings_field(
                          PF_B_Manager::$action.'_info_blocks',
                          __( 'Manage Info Blocks', PF_B_Manager::$action ),
                          array( __CLASS__, 'field' ),
                          PF_B_Manager::$action,
                          PF_B_Manager::$action.'_section_customisation',
                          array(
                                  'name'        =>  'info_blocks',
                                  'type'        =>  'info-block',
                                )
                        );

      /* END INIT FIELDS */
    }

    /**
     * Function that will validate all fields.
     * 
     * @param  array $fields [description]
     * 
     * @return array
     *
     * @since 1.0.0.0
     * 
     */
    public static function validate_options( $fields )
    {
      $valid_fields = array();

      $valid_fields = array_combine( array_keys( $fields ), array_map( function( $value ){
        return is_array( $value ) ?
          array_combine( array_keys( $value ) , array_map( function( $value ){
            return htmlspecialchars( $value );
          }, $value ) )
          : htmlspecialchars( $value );
      }, $fields ) );  

      // check to INT value
      foreach( 
              array( 
                  'elements_per_page' => __( 'Number of families to display', PF_B_Manager::$action ),
                  'info_blocks'       =>  array(
                                              'key'   =>  'position',
                                              'label' =>  __( 'Info Blocks position', PF_B_Manager::$action )
                                                )
                    ) 
              as $key => $label 
            )
        if( 
          ( !is_array( $label ) && isset( $valid_fields[ $key ] ) && (int)$valid_fields[ $key ] == 0 && $valid_fields[ $key ] != '0' )
          ||
          ( is_array( $label ) && isset( $valid_fields[ $key ][ $label['key'] ] ) && (int)$valid_fields[ $key ][ $label['key'] ] == 0 && $valid_fields[ $key ][ $label['key'] ] != '0' )
          ):
          add_settings_error( PF_B_Manager::$action.'_messages', PF_B_Manager::$action.'_message', sprintf( __( 'Invalid value for %s', PF_B_Manager::$action ), !is_array( $label ) ? $label: $label['label'] ), 'error' );
          if( !is_array( $label ) )
            $valid_fields[ $key ] = PF_B_Manager::$options[ $key ];
          else
            $valid_fields[ $key ][ $label['key'] ] = PF_B_Manager::$options[ $key ][ $label['key'] ]; 
        endif;

      return apply_filters( 'validate_options', $valid_fields, $fields);
    }

    /**
     * render additional html for section
     * @param  array  $args  settings
     * 
     * @since  1.0.0.0
     */
    public static function render_section( $args ) {}

    /**
     * generate HTML code for field by type
     * 
     * @param  array  $args array of settings
     * 
     * @since 1.0.0.0
     * 
     */
    public static function field( $args )
    {
      PF_B_Helpers_View::get_template_part( 'settings/fields/'.$args['type'], $args );
    }
  }