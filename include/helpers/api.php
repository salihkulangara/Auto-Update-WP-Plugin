<?php
  class PF_B_Helpers_API
  {
    /**
     * create embed link
     * 
     * @param  string $id ID of video
     * @param  integer $youtube is youtube?
     * 
     * @since  1.0.0.0
     * 
     */
    public static function video_embed( $id, $youtube )
    {
      return $youtube == 1 ? 'https://www.youtube.com/embed/'.self::get_youtube_id( $id ) : $id;
    }

    /**
     * return ID of youtube video
     * 
     * @param  string $url
     * 
     * @return string 
     *
     * @since 1.0.0.0
     * 
     */
    protected static function get_youtube_id( $url )
    {
      $id = ''; 
      if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match))
          $id = $match[1];

      return $id;
    }

    /**
     * check is parent enable for show?
     *
     * @param  $label array
     * 
     * @return boolean(int)
     *
     * @since 1.0.3.12
     * 
     */
    public static function check_is_enable_profile( $label )
    {
      if( !isset( PF_B_Manager::$options['disable_in_progress'] ) || PF_B_Manager::$options['disable_in_progress'] != TRUE )
        return TRUE;

      if( !is_array( $label ) || !isset( $label['type'] ) )
        return TRUE;

      return $label['type'] == 'profile in progress' ? FALSE : TRUE;
    }

    /**
     * create new string with $count words
     * 
     * @param  string   $text
     * @param  integer  $count
     * @param  string   $append
     * 
     * @return string
     * 
     */
    public static function crop_str_word( $text, $count = 140, $append = ' ...' )
    {
       $count += 1;
       
       $words = explode( ' ', $text, $count );
       array_pop( $words );
       
       return implode( ' ', $words ).$append;
    }
  }