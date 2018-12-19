<?php
    class PF_B_Gateway_Profile extends PF_B_Gateway
    {
        /**
         * url to get profile data
         * format https://www.parentfinder.com/api/getFamilyDetails/{username}
         * 
         * @var string
         *
         * @since 1.0.0.0
         * 
         */
        public static $profile_url = 'getFamilyDetails/{username}';

        /**
         * username
         * 
         * @var integer
         *
         * @since 1.0.0.0
         * 
         */
        public static $username = '';

        /**
         * answer API server
         * 
         * @var null
         *
         * @since  1.0.2.0
         */
        protected static $data = NULL;

        /**
         * show json answer to request
         * 
         * @since 1.0.0.0
         * 
         */
        public static function render()
        {
            $request = PF_B_Gateway::_request( 
                                                self::$profile_url, 
                                                array( 'username' => self::$username )  
                                            );

            if( $request === FALSE )
                return '[]';

            self::$data = $request->data;
            $profile = self::$data->profile;  
            
            $data = array( 
                      'info'        =>  array(
                                            'avatar'                =>  $profile->parent1->avatarImage,
                                            'short_description'     =>  self::get_short_description( $profile->letter ),
                                            'video'                 =>  self::homevideo(),
                                            'flipbook'              =>  self::flipbook(),
                                            'download'              =>  self::pdf(),
                                            'username'              =>  $profile->parent1->username,
                                            'account_id'            =>  (int)$profile->parent1->account_id,
                                            'website'               =>  !empty( $profile->contactDetails->website ) ? preg_replace( '/^(?!https?:\/\/)/', 'http://', $profile->contactDetails->website ) : '',
                                            'is_enable'             =>  (int)PF_B_Helpers_API::check_is_enable_profile( isset( $profile->parent1->isProgress ) && $profile->parent1->isProgress ? array( 'type' => 'profile in progress' ) : NULL )
                                            ), 
                      'agency'      =>  $profile->agency,
                      /*'vitals'      =>  array(
                                            'childpreferences'      =>  $profile->childpreferences,
                                            'waiting'               =>  $profile->parent1->waiting,
                                            ),*/
                      'journals'    =>  empty( $profile->journal ) ? array() : $profile->journal,
                      'letters'     =>  empty( $profile->letter ) ? array() : self::from_letters_to_journals( $profile->letter ),
                      'photos'      =>  self::photos(),
                      'videos'      =>  self::videos(),
                        );
            
            $data['vitals'] = [];
            $childpreferences = [];
            $options = PF_B_Manager::$options;
            $data['displayOptions'] = $options;

            if($options['show_child_ethnicity'] == 'show' && $profile->childpreferences->ethnicity)
                $childpreferences['ethnicity'] = $profile->childpreferences->ethnicity;
            if($options['show_child_age'] == 'show' && $profile->childpreferences->ageGroup)
                $childpreferences['ageGroup'] = $profile->childpreferences->ageGroup;
            if($options['show_adoption_type'] == 'show' && $profile->childpreferences->adoption)
                $childpreferences['adoption'] = $profile->childpreferences->adoption;

            $data['vitals']['childpreferences'] = $childpreferences;

            if($options['show_waiting'] == 'show')
                $data['vitals']['waiting'] = $profile->parent1->waiting;

            if($options['show_state'] == 'show') {
                $isUS = $profile->contactDetails->country == 'United States' ? 1 : 0;
                $data['vitals']['state'] = $isUS ? $profile->contactDetails->state : $profile->contactDetails->country;
                $data['vitals']['stateLabel'] = $isUS ? 'State' : 'Country';
            }

            if($options['show_childrens_age'] == 'show' && $profile->noOfChildren)
                $data['vitals']['childrensAge'] = $profile->childrensAge;

            if($options['show_no_of_children'] == 'show')
                $data['vitals']['no_of_children'] = !$profile->noOfChildren ? 'No children' : $profile->noOfChildren;

            for( $i = 1; $i <= 2; $i ++ ):
                $parent_key = 'parent'.$i;
                $parent = array();
                
                foreach( array( 'first_name' => '', 'religion' => 'show_religion', 'ethnicity' => 'show_family_ethnicity', 'education' => 'show_education', 'gender' => 'show_gender', 'dob' => '', 'age' => 'show_family_age' ) as $key => $show )
                    if( isset( $profile->$parent_key->$key ) )
                        if( $show == '' || $options[$show] == 'show' )
                            $parent[ $key ] = $profile->$parent_key->$key;
                
                if( !empty( $parent ) )
                    $data['vitals'][$parent_key] = $parent;

                unset( $parent, $parent_key );
            endfor;

            return json_encode( $data );
        }

        /**
         * get homevideo
         * 
         * @return string
         *
         * @since 1.0.0.0
         * 
         */
        protected static function homevideo()
        {
            $video = '';

            if( !isset( self::$data->homeVideos[0] ) )
                return $video;

            $request = self::$data->homeVideos[0];

            return PF_B_Helpers_API::video_embed( $request->Uri, $request->YoutubeLink );
        }

        /**
         * url to get PDF document
         * 
         * @return string
         *
         * @since 1.0.0.0
         * 
         */
        protected static function pdf()
        {
            return !isset( self::$data->pdfProfile ) || !isset( self::$data->pdfProfile->multi_profile ) ? '' : self::$data->pdfProfile->multi_profile;
        }

        /**
         * url to get FlipBook
         * 
         * @return string
         *
         * @since 1.0.0.0
         * 
         */
        protected static function flipbook()
        {
            $flipbook = '';

            if( !isset( self::$data->flipBook ) )
                return $flipbook;

            return isset( self::$data->flipBook->flip_book ) ? self::$data->flipBook->flip_book : '';
        }

        /**
         * url to get all videos for username
         * 
         * @return array
         *
         * @since 1.0.0.0
         * 
         */
        protected static function videos()
        {
            $videos = array();

            if( !isset( self::$data->videoAlbums ) )
                return $videos;

            foreach( self::$data->videoAlbums as $video )
                $videos[] = array( 'label' => $video->Caption, 'video' => PF_B_Helpers_API::video_embed( $video->Uri, $video->youtube ) );
            
            return $videos;
        }

        /**
         * filter letters for get about us message
         * 
         * @param  array $letters
         * 
         * @return string
         *
         * @since 1.0.0.0
         * 
         */
        protected static function get_short_description( $letters )
        {
            $message = '';
            
            foreach( $letters as $letter ):
                if( $letter->Title == 'LETTER ABOUT THEM' )
                    $message = $letter->Content;
                if( (int)$letter->isIntroduction )
                    $message = $letter->Content;
            endforeach;

            $message = strip_tags( $message );

            return empty( $message ) ? '' : PF_B_Helpers_API::crop_str_word( $message );
        }

        /**
         * url to get all photos for username
         * 
         * @return array
         *
         * @since 1.0.0.0
         * 
         */
        protected static function photos()
        {
            $photos = array();

            if( !isset( self::$data->albumPhotos ) )
                return $photos;

            foreach( self::$data->albumPhotos as $photo ):
                if( !isset( $photo->webviewImage ) || empty( $photo->webviewImage ) )
                    continue;
                $photos[] = array( 'full' => $photo->webviewImage, 'thumb' => $photo->webviewImage );
            endforeach;

            return $photos;
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
            if( !isset( $_REQUEST['username'] ) )
                return FALSE;

            self::$username = htmlspecialchars( $_REQUEST['username'] );

            if( empty( self::$username ) )
                return FALSE;

            return TRUE;
        }

        /**
         * convert letters json to journal json
         * 
         * @param array $letters
         * 
         * @return array          
         *
         * @since 1.0.0.0
         * 
         */
        protected static function from_letters_to_journals( $letters )
        {   
            $data = array();
        
            foreach( $letters as $letter )
                $data[] = array(
                            'journalId'         =>  $letter->postid,
                            'Caption'           =>  $letter->Title,
                            'Text'              =>  $letter->Content,
                            'Uri'               =>  '',
                            'Photo'             =>  $letter->Image,
                            'journdate'         =>  '',
                                );

            return $data;
        }
    }
