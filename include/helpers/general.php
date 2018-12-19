<?php
  class PF_B_Helpers_General
  {
    /**
     * function for call instance method of class
     * 
     * @param  string $class name of class
     * @param  string $path  path to class file
     * 
     * @since  1.0.0.0
     * 
     */
    public static function call_instance( $class, $path )
    {
      include_once( PF_B_Manager::$path.'classes/'.$path.'.php' );
      $class::_instance();
    }

    /**
     * convert array to tinymce options array for select
     * 
     * @param  array $options
     * @param  boolean $has_keys
     * 
     * @return array         
     *
     * @since 1.0.1.0
     * 
     */
    public static function array_to_tinymce_select_options( $options, $has_keys = false )
    {
      $data = array();
      foreach( $options as $key => $option )
        $data[] = array( 'text' => str_replace( '_', ' ', $option ), 'value' => $has_keys ? $key : $option );

      return $data;
    }

    /**
     * generate array of all WordPress elements by post_type
     * 
     * @param  array  $post_types
     * 
     * @return array
     *
     * @since 1.0.1.0
     * 
     */
    public static function get_elements( $post_types = array( 'page' ) )
    {
      $elements = array();
      foreach( get_posts( array( 'post_type' => $post_types, 'post_status' => 'publish', 'sort_column' => 'post_parent,menu_order', 'suppress_filters' => 0 ) ) as $element )
        $elements[ $element->ID ] = $element->post_title;
      return $elements;
    }

    /**
     * check existing VisualComposer
     * 
     * @return boolean
     *
     * @since 1.0.1.0
     * 
     */
    public static function has_vc()
    {
      return class_exists( 'WPBMap' );
    }
  }