<?php
  class PF_B_Helpers_Featured
  {
    /**
     * returned featured parents by rules
     * 
     * @param  array $atts
     * 
     * @return array
     *
     * @since 1.0.1.0
     * @since 1.0.1.2 added random order
     * 
     */
    public static function profiles( $atts )
    {
      include_once( PF_B_Manager::$path.'classes/gateway/data.php' );
      $parents = PF_B_Gateway_Data::get_parents_array();

      /**
       * filter for matched/placed/both
       */
      $parents = array_filter( $parents, function( $profile ) use( $atts ){
        if( empty( $profile['label'] ) )
          return FALSE;
        
        if( $atts['type'] == 'both' || $profile['label']['type'] == $atts['type'] )
          return TRUE;

        return FALSE;
      });

      /**
       * sort parents by order
       * random, oldest, youngest, first name
       */
      if( $atts['order'] == 'random' )
        shuffle( $parents );
      else
        usort( $parents, function( $profile1, $profile2 ) use( $atts ){
          if( $atts['order'] == 'first_name' )
            return strnatcasecmp( self::get_names( $profile1 ), self::get_names( $profile2 ) );
          else
            return ( $atts['order'] == 'oldest' ? -1 : 1 ) * ( $profile1['account_id'] <= $profile2['account_id'] ? -1 : ( $profile1['account_id'] == $profile2['account_id'] ? 0 : 1 ) );
        });
      
      return array_splice( $parents, 0, $atts['count'] );
    }

    /**
     * generate full name of profile
     * 
     * @param  array $profile
     * 
     * @return string
     *
     * @since 1.0.1.0
     * 
     */
    public static function get_names( $profile )
    {
      $names = array();
      for( $i = 1; $i <= 2; $i ++ )
        if( isset( $profile[ 'parent'.$i ][ 'first_name' ] ) && !empty( $profile[ 'parent'.$i ][ 'first_name' ] ) )
          $names[] = $profile[ 'parent'.$i ][ 'first_name' ];

      return implode( $names, ' & ' );
    }

    /**
     * generate wrapper for featured profile
     * 
     * @param  array $profile
     * @param  array $atts   
     * 
     * @return string       
     *
     * @since  1.0.1.0
     * 
     */
    public static function get_profile_wrapper( $profile, $atts )
    {
      $grid = get_permalink( $atts[ 'grid' ] );
      $classes = PF_B_Manager::$action.'-featured-profile';

      return empty( $atts['grid'] ) || empty( $grid ) ? '<div class="'.$classes.'">%s</div>' : '<a href="'.$grid.'/#/profile/'.$profile['username'].'" class="'.$classes.'">%s</a>';
    }
  }