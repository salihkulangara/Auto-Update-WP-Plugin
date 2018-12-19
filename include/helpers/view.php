<?php
  class PF_B_Helpers_View
  {
    /**
     * get template part from views folder
     * 
     * @param  string    $template    Template name
     * @param  array     $args        Array of args
     * @param  string    $ext         Extension of file
     * @param  boolean   $dashboard   Dashboard or Frontend
     * 
     * @since  1.0.0.0
     * 
     */
    public static function get_template_part( $template, $args = array(), $ext = 'php', $dashboard = true )
    {
      if( !empty( $args ) )
        extract( $args );

      include( PF_B_Manager::$assets_path.( $dashboard ? 'dashboard' : 'frontend' ).'/views/'.$template.'.'.$ext );
    }

    /**
     * load template part from views folder in variable
     * 
     * @param  string    $template    Template name
     * @param  array     $args        Array of args
     * @param  string    $ext         Extension of file
     * @param  boolean   $dashboard   Dashboard or Frontend
     *
     * @return string
     * 
     * @since  1.0.0.0
     */
    public static function load_to_variable( $template, $args = array(), $ext = 'php', $dashboard = true )
    {
      ob_start();
        self::get_template_part( $template, $args, $ext, $dashboard );
      $content = ob_get_contents();  
      ob_end_clean(); 

      return $content;
    }

    /**
     * get image url for info block
     * 
     * @param  integer $id   ID of image
     * @param  string  $size small||large
     * 
     * @return string
     *
     * @since 1.0.0.0
     * 
     */
    public static function get_info_block_background_image_url( $id, $size )
    {
      if( empty( $id ) )
        return '';
      
      $image = wp_get_attachment_image_src( $id, PF_B_Manager::$action.'_'.PF_B_Manager::$info_block_action_suffix.'_'.$size );
      
      return isset( $image[0] ) ? $image[0] : '';
    }
  }