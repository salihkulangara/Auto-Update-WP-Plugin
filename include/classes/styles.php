<?php
  class PF_B_Styles
  { 
    protected static $_instance = NULL;

    /**
     * array of styles
     * 
     * @var array
     *
     * @since 1.0.0.0
     * 
     */
    protected static $styles = array();

    /**
     * generate css from scss and admin settings
     * 
     * @return NULL
     *
     * @since 1.0.0.0
     * 
     */
    public static function css()
    {
      require PF_B_Manager::$path.'modules/scssphp/scss.inc.php';

      $scss = new Leafo\ScssPhp\Compiler();

      $scss->setFormatter( "Leafo\ScssPhp\Formatter\Compressed" );

      $scss->setImportPaths( PF_B_Manager::$assets_path.'frontend/css/' );

      $scss->setVariables( 
                          self::variables( 
                                          array( 
                                                'font-family'           =>  PF_B_Manager::$options['font_family'] != 'theme' ? 'Avenir, sans-serif' : 'inherit',
                                                'primary-color'         =>  PF_B_Manager::$options['primary_color'],
                                                'secondary-color'       =>  PF_B_Manager::$options['secondary_color'],
                                                'favorite-color-hover'  =>  PF_B_Manager::$options['secondary_color'],
                                                ) 
                                          ) 
                          );

      echo $scss->compile( '@import "theme.scss";' );
    }

    /**
     * generate array of variables for scss based on default 
     * settings and settins from admin area
     * 
     * @return array
     *
     * @since 1.0.0.0
     * 
     */
    protected static function variables( $settings )
    {
      $settings = array_filter( $settings, 'strlen' );

      $variables = array(
                  'font-family'                   =>  'inherit',
                  'gutter'                        =>  '20px',
                  'primary-color'                 =>  '#bbdc9b',
                  'secondary-color'               =>  '#f5b3d1',
                  'favorite-color-hover'          =>  '#f5b3d1',
                  'font-color'                    =>  'white',
                  'font-color-secondary'          =>  '#000',
                  'adv-bg-color'                  =>  '#7ebe47',
                  'app-font-size'                 =>  '16px',
                  'journal-background'            =>  '#f4f0f2',
                  'journal-color'                 =>  '#515252',
                  'journal-date-color'            =>  '#9a9ea3',
                  'form-bg-color'                 =>  '#F4F0F2',
                  'form-border-color'             =>  '#d2d7dc',
                  'form-bg-secondary-color'       =>  '#e8eef1',
                  'form-border-secondary-color'   =>  '#d2d7dc',
                  'checkboxBg'                    =>  '#fff',
                  'checkColor'                    =>  '$primary-color',
                  'errorColor'                    =>  '$primary-color',
                  'successColor'                  =>  '$secondary-color',
                    );

      return array_merge( $variables, $settings );
    }

    private function __construct(){}

    private function __clone() {}

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }
  }