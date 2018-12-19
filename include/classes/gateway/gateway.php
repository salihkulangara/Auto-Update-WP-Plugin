<?php
  class PF_B_Gateway
  {
    /**
     * base part url for gateway
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    public static $gateway_base = 'https://parentfinder.com/api/';

    /**
     * url to get agency data by api key
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    public static $agency = 'oauth/user';

    /**
     * url to gateway
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    public static $gateway_url = '';

    /**
     * show json answer to request
     *
     * @return string
     * 
     * @since 1.0.0.0
     * 
     */
    public static function render() {}

    /**
     * function for send request to remote server
     * 
     * @param  string  $url     
     * @param  array   $args     
     * @param  array   $variables
     * @param  integer $timeout 
     * 
     * @return string||Boolean
     *
     * @since 1.0.0.0
     * 
     */
    static protected function _request( $url, $variables = array(), $args = array( 'callback' => '' ), $post = 0, $timeout = 60 )
    {
      $args = array(
                  'method'  =>  $post ? 'POST' : 'GET',
                  'timeout' =>  $timeout,
                  'body'    =>  $args
                    );

      if( strpos( $url, 'http' ) === FALSE )
        $url = self::$gateway_base.$url;

      if( !empty( $variables ) )
        $url = self::generate_url( $url, $variables );

      $request = wp_remote_post( $url, $args );
      
      if( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 )
      {
        $request['body'] = ltrim( $request['body'], '(' );
        $request['body'] = rtrim( $request['body'], ')' );
        
        $json = json_decode( $request['body'] );
        unset( $request['body'] );

        return $json == NULL || empty( $json ) ? FALSE : $json;
      }else
        return FALSE;
    }

    /**
     * replace variables in url
     * 
     * @param  string $url  
     * @param  array $args
     * 
     * @return string
     *
     * @since 1.0.0.0
     * 
     */
    protected static function generate_url( $url, $args )
    {
        return str_replace( 
                            array_map( 
                                        function( $a ){
                                                    return '{'.$a.'}';
                                        },
                                        array_keys( $args ) 
                                    ), 
                            array_values( $args ), 
                            $url 
                        );
    }

    /**
     * check api key from plugin dashboard
     * 
     * @param  string $key    
     * @param  string $agency
     * 
     * @return boolean
     *
     * @since 1.0.0.0
     * 
     */
    public static function check_api_key( $key, $agency )
    {
        if( empty( $key ) )
            return FALSE;

        $request = self::_request( self::$agency, array(), array( 'oauthToken' => $key ) );
        if( $request === FALSE || !isset( $request->data->agency->agency_id ) )
            return FALSE;

        return (int)$request->data->agency->agency_id == (int)$agency ? TRUE : FALSE;
    }

    /**
     * chech access to gateway
     * validate arguments
     * 
     * @return boolean
     *
     * @since 1.0.0.0
     * 
     */
    public static function check_access()
    {
      return TRUE;
    }
  }
