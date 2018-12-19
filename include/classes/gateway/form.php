<?php
  class PF_B_Gateway_Form extends PF_B_Gateway
  {
    /**
     * url to get profile data
     * format https://www.parentfinder.com/api/profile/{username}
     * 
     * @var string
     *
     * @since 1.0.0.0
     * 
     */
    protected static $profile_url = 'publicProfile/{username}';

    /**
     * username
     * 
     * @var integer
     *
     * @since 1.0.0.0
     * 
     */
    protected static $username = '';

    /**
     * type of form
     * 
     * @var integer
     *
     * @since 1.0.0.0
     * 
     */
    protected static $form_type = '';

    /**
     * fields for each form
     * 
     * @var array
     *
     * @since 1.0.0.0
     * 
     */
    protected static $fields = array(
                                  'call'    =>  array( 
                                                    'time'            => '', 
                                                    'phone_number'    => '' 
                                                    ),
                                  'chat'    =>  array( 
                                                    'phone_number'    => '', 
                                                    'message'         => '' 
                                                    ),
                                  'email'   =>  array( 
                                                    'your_email'      => '', 
                                                    'message'         => ''
                                                    ),
                                    );

    /**
     * data of recipient
     * 
     * @var array
     *
     * @since 1.0.0.0
     * 
     */
    protected static $send_to = array( 'title' => '', 'email' => '' );

    /**
     * return json answer to request
     * 
     * @since 1.0.0.0
     * 
     */
    public static function render()
    {
      require_once PF_B_Manager::$path.'modules/phpmailer/PHPMailerAutoload.php';

      /**
       * array of subjects 
       * 
       * @var array
       *
       * @since 1.0.0.0
       * 
       */
      $subjects = array(
                        'email' =>  __( 'Message from {your_email} for user {username}', PF_B_Manager::$action ),
                        'chat'  =>  __( 'Message from {phone_number} for user {username}', PF_B_Manager::$action ),
                        'call'  =>  __( 'Call Request from {phone_number}', PF_B_Manager::$action )
                          );

      $mail = new PHPMailer;
      $mail->isHTML(true);

      $mail->SetFrom( 'no-reply@parentfinder.com', 'No-Reply ParentFinder' );

      $mail->addAddress( self::$send_to['email'], self::$send_to['title'] );

      $mail->AddCC( 'info@parentfinder.com', 'ParentFinder' );
      
      $fields = array_merge( array( 'username' => self::$username ), self::$fields[ self::$form_type ] );
      $mail->Subject = self::generate_url( $subjects[ self::$form_type ], $fields );
      $mail->Body = self::get_email_template( $fields );

      self::prerender( $mail->send() ? 'success' : 'error' );
      return '';
    }

    /**
     * load email template and replacing fields
     * 
     * @param  array $fields
     * 
     * @return string
     *
     * @since 1.0.0.0
     * 
     */
    protected static function get_email_template( $fields )
    {
      return PF_B_Helpers_View::load_to_variable( 'emails/'.self::$form_type, $fields );
    }

    /**
     * show json answer to request
     * 
     * @since 1.0.0.0
     * 
     */
    protected static function prerender( $status = '' )
    {
      echo json_encode( array( 'status' => $status ) );
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
      if( !isset( $_REQUEST['username'] ) || !isset( $_REQUEST['form_type'] ) )
      {
        self::prerender( 'not_valid' );
        return FALSE;
      }

      self::$username = htmlspecialchars( $_REQUEST['username'] );
      self::$form_type = htmlspecialchars( $_REQUEST['form_type'] );

      if( empty( self::$username ) || empty( self::$form_type ) )
      {
        self::prerender( 'not_valid' );
        return FALSE;
      }

      if( !in_array( self::$form_type, array_keys( self::$fields ) ) )
      {
        self::prerender( 'not_valid' );
        return FALSE;
      }

      foreach(  array_keys( self::$fields[ self::$form_type ] ) as $key )
        if( !isset( $_REQUEST[ $key ] ) || empty( htmlspecialchars( $_REQUEST[ $key ] ) ) )
        {
          self::prerender( 'not_valid' );
          return FALSE;
        }else
          self::$fields[ self::$form_type ][ $key ] = htmlspecialchars( $_REQUEST[ $key ] );

      $request = PF_B_Gateway::_request( 
                                              self::$profile_url, 
                                              array( 'username' => self::$username )  
                                          );

      if( $request === FALSE )
      {
        self::prerender( 'error' );
        return FALSE;
      }

      if( !isset( $request->profiles[0]->profile->agency->email ) || empty( $request->profiles[0]->profile->agency->email ) )
      {
        self::prerender( 'error' );
        return FALSE;
      }

      self::$send_to = array( 'title' => $request->profiles[0]->profile->agency->title, 'email' => mb_strtolower( $request->profiles[0]->profile->agency->email ) );
      
      return TRUE;
    }
  } 
