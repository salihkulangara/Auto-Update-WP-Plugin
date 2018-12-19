<?php
  class PF_B_Gateway_Routing
  {
    protected static $_instance = NULL;

    /**
     * list of avaible request types
     * 
     * @var array
     *
     * @since 1.0.0.0
     * 
     */
    protected static $avaible_requests = array( 'data', 'profile', 'form' );

    private function __construct( $request )
    {
      if( !in_array( $request, self::$avaible_requests ) )
        return;


      $path = PF_B_Manager::$path.'classes/gateway/'.$request.'.php';
      if( !file_exists( $path ) )
        return;
      
      include_once( $path );
      $action = 'PF_B_Gateway_'.mb_convert_case( $request, MB_CASE_TITLE );

      if( !class_exists( $action ) )
        return;

      if( $action::check_access() )
        echo $action::render();
    }

    private function __clone() {}

    public static function _instance( $request )
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self( $request );

      return self::$_instance;
    }
  }