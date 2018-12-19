<?php
class PF_B_Gateway_Data extends PF_B_Gateway
{
  /**
   * url for get all parents
   * 
   * @var string
   *
   * @since 1.0.0.0
   * 
   */
  public static $list_url = 'getBadgeProfiles?callback=&agencyId={agency_id}&random_ids={random_ids}';

  /**
   * data required from API
   * 
   * @var array
   *
   * @since 1.0.0.0
   * 
   */
  static $data = array(
    'parents' => array('id' => array()),
    'filters' => array(
      'religion' => array(),
      'waiting' => array(),
      'location' => array(),
      'avatarLabel' => array(),
      'kidsInFamily' => array()
    )
  );

  /**
   * list of parents ID
   * 
   * @var array
   *
   * @since 1.0.0.0
   * 
   */
  static $parent_ids = array();

  /**
   * settings for badges
   * 
   * @var array||NULL
   *
   * @since 1.0.0.0
   * 
   */
  protected static $badges = null;

  /**
   * show json answer to request
   * 
   * @since 1.0.0.0
   * 
   */
  public static function render()
  {
    $data = array(
      'options' => array(),
      'captions' => array(),
      'filters' => array(),
      'ads' => array(),
      'parents' => array()
    );

    /**
     * TRANSLATIONS
     */
    $data['captions'] = array(
      'more' => __('Load More Families', PF_B_Manager::$action),
      'advMore' => __('Read More', PF_B_Manager::$action),
      'contact' => '',
      'seeProfile' => __('See Profile', PF_B_Manager::$action),
      'disableProfile' => __('Profile in progress. Please check back soon.', PF_B_Manager::$action),
      'notFoundProfile' => __('Profile not found.', PF_B_Manager::$action),
      'searchPlaceholder' => __('Search by Name', PF_B_Manager::$action),
      'location' => __('Country/State', PF_B_Manager::$action),
      'religion' => __('Religion', PF_B_Manager::$action),
      'waiting' => __('Waiting', PF_B_Manager::$action),
      'kidsInFamily' => __('Kids in family', PF_B_Manager::$action),
      'avatarLabel' => __('Status', PF_B_Manager::$action),
      'sort_by' => __('Sort by', PF_B_Manager::$action),
      'random' => __('Random', PF_B_Manager::$action),
      'oldest' => __('Oldest waiting', PF_B_Manager::$action),
      'youngest' => __('Youngest waiting', PF_B_Manager::$action),
      'first_name' => __('First Name', PF_B_Manager::$action),
      'last_name' => __('Last Name', PF_B_Manager::$action),
      'Selected' => __('Selected', PF_B_Manager::$action),
      'json_error' => __('Error get data', PF_B_Manager::$action),
      'not_found' => __('Not found', PF_B_Manager::$action),
      'profile' => array(
        'contact_us' => __('Contact Us 24x7', PF_B_Manager::$action),
        'back_to_search' => __('Back to Search', PF_B_Manager::$action),
        'phone' => __('Phone', PF_B_Manager::$action),
        'download' => __('Download', PF_B_Manager::$action),
        'email' => __('Email', PF_B_Manager::$action),
        'website' => __('WebSite', PF_B_Manager::$action),
        'chat' => __('Chat', PF_B_Manager::$action),
        'flipbook' => __('Flipbook', PF_B_Manager::$action),
        'more_photos' => __('See more photos', PF_B_Manager::$action),
        'more_videos' => __('See more videos', PF_B_Manager::$action),
        'watch' => __('Watch our video', PF_B_Manager::$action),
        'vitals' => __('Vitals', PF_B_Manager::$action),
        'journals' => __('Journals', PF_B_Manager::$action),
        'letters' => __('Letters', PF_B_Manager::$action),
        'about_us' => __('About Us', PF_B_Manager::$action),
        'videos' => __('Videos', PF_B_Manager::$action),
        'preferences' => __('Our adoption preferences', PF_B_Manager::$action),
        'ethnicity' => __('Ethnicity', PF_B_Manager::$action),
        'age' => __('Age', PF_B_Manager::$action),
        'adoption_type' => __('Adoption type', PF_B_Manager::$action),
        'religion' => __('Religion', PF_B_Manager::$action),
        'education' => __('Education', PF_B_Manager::$action),
        'gender' => __('Gender', PF_B_Manager::$action),
        'waiting' => __('Waiting', PF_B_Manager::$action),
        'no_of_children' => __('Number of children', PF_B_Manager::$action),
        'childrens_age' => __('Ages of children', PF_B_Manager::$action),
        'nothing_to_show' => __('Nothing to show', PF_B_Manager::$action),
      ),
      'forms' => array(
        'error' => __('Error of sending form', PF_B_Manager::$action),
        'success' => __('Form submitted', PF_B_Manager::$action),
        'not_valid' => __('Invalid data', PF_B_Manager::$action),
        'labels' => array(

          'time' => array(
            __('Now', PF_B_Manager::$action),
            __('Morning', PF_B_Manager::$action),
            __('Afternoon', PF_B_Manager::$action),
            __('Evening', PF_B_Manager::$action),
          ),
          'or' => __('or', PF_B_Manager::$action),
          'call_me_back' => __('Call me back', PF_B_Manager::$action),
          'number' => __('Phone number', PF_B_Manager::$action),
          'message' => __('Message', PF_B_Manager::$action),
          'chat' => __('Chat', PF_B_Manager::$action),
          'email' => __('Your email', PF_B_Manager::$action),
          'send_mail' => __('Send email', PF_B_Manager::$action),
          'submit' => __('Submit', PF_B_Manager::$action),
        )
      ),
      'tooltips' => array(
        'navigation' => __('Use these navigation icons for quick access to contacts, downloads and other information.', PF_B_Manager::$action),
        'photos' => __('Click on any photo to view the gallery. You can also click SEE MORE PHOTOS to view photo gallery from the beginning.', PF_B_Manager::$action),
        'video' => __('Click on video to play or use SEE MORE VIDEOS link to view all available videos.', PF_B_Manager::$action),
        'content' => __('Click on section headings to view extra content.', PF_B_Manager::$action),
      ),
      'tooltips_buttons' => array(
        'skip' => __('skip', PF_B_Manager::$action),
        'done' => __('done', PF_B_Manager::$action),
        'next' => __('next', PF_B_Manager::$action),
        'prev' => __('prev', PF_B_Manager::$action),
      )
    );

    /**
     * GENERAL OPTIONS
     */
    $data['options'] = array(
      'adsRange' => (int)(!(bool)PF_B_Manager::$options['info_blocks']['show'] ? 0 : PF_B_Manager::$options['info_blocks']['position']),
      'itemsPerPage' => (int)PF_B_Manager::$options['elements_per_page'],
      'visibleCount' => (int)PF_B_Manager::$options['elements_per_page'],
      'display_order' => PF_B_Manager::$options['display_order'],
      'sorting_options' => PF_B_Manager::$options['sorting_options']
    );

    if (self::get_parents() === false)
      return json_encode($data);

    /**
     * FILTERS
     */
    $data['filters'] = self::filters(self::$data['filters'], $data);

    /**
     * Advertising
     */
    if ((bool)PF_B_Manager::$options['info_blocks']['show'])
      $data['ads'] = array_map(
      function ($adv) {
        $adv->type = 'adv';
        $adv->image = PF_B_Helpers_View::get_info_block_background_image_url($adv->image, $adv->size);
        return $adv;
      },
      PF_B_Manager::$options['info_blocks']['data']
    );

    /**
     * Parents
     */
    $data['parents'] = self::$data['parents'];

    return json_encode($data);
  }

  /**
   * get all parents info from API
   * all value added to self::$data
   * 
   * @return boolean
   *
   * @since 1.0.0.0
   * 
   */
  protected static function get_parents()
  {
    $index = 0;

    $request = PF_B_Gateway::_request(
      self::$list_url,
      array(
        'agency_id' => PF_B_Manager::$options['agency_id'],
        'random_ids' => implode(',', self::$parent_ids),
      ),
      array(
        'callback' => '',
        'agency_id' => PF_B_Manager::$options['agency_id'],
        'random_ids' => implode(',', self::$parent_ids),
      ),
      1
    );

    if ($request === false || empty($request->profiles))
      return empty(self::$parent_ids) ? false : true;

    foreach ($request->profiles as $profile) {
      self::$parent_ids[] = $profile->accountId;

      self::$data['parents']['id'][] = array(
        'type' => 'person',
        'id' => $index,
        'account_id' => (int)$profile->accountId,
        'filters' => self::get_filters_value($profile),
        'img' => $profile->avatarImage,
        'parent1' => array(
          'first_name' => self::get_name($profile->name, 1)
        ),
        'parent2' => array(
          'first_name' => self::get_name($profile->name, 2)
        ),
        'location' => array(
          'country' => $profile->country,
          'state' => $profile->state
        ),
        'username' => $profile->userName,
        'label' => self::get_badge($profile->avatarLabel),
        'tag' => self::get_name($profile->name, 1) . self::get_name($profile->name, 2, ' ')
      );

      $index_last = sizeof(self::$data['parents']['id']) - 1;
      self::$data['parents']['id'][$index_last]['is_enable'] = (int)PF_B_Helpers_API::check_is_enable_profile(self::$data['parents']['id'][$index_last]['label']);

      if (!isset(self::$data['filters']['location'][$profile->country]))
        self::$data['filters']['location'][$profile->country] = array();

      if (!empty($profile->state) && !in_array($profile->state, self::$data['filters']['location'][$profile->country]))
        self::$data['filters']['location'][$profile->country][] = $profile->state;


      unset($index_last);
      $index++;
    }
      
      /* Sort filters */
    if (!empty(self::$data['filters']))
      foreach (self::$data['filters'] as $group => $filters)
      if ($group == 'location') {
      ksort(self::$data['filters'][$group]);
      foreach ($filters as $key_filter => $filter)
        usort(self::$data['filters'][$group][$key_filter], 'strnatcmp');
    } elseif ($group == 'waiting')
      usort(self::$data['filters'][$group], function ($a, $b) {
      $weights = array('Less than 6 months' => 1, 'Between 6 - 12 months' => 2, '1 year' => 3, '2 years' => 4, '3 years' => 5, 'more than 3 years' => 6);

      if (!isset($weights[$a]) || !isset($weights[$b]))
        return 0;

      return $weights[$a] < $weights[$b] ? -1 : 1;
    });
    else
      usort(self::$data['filters'][$group], 'strnatcmp');
  }

  /**
   * get placed/matched badge settings
   * 
   * @param  string $type type of badge
   * 
   * @return array||string
   *
   * @since 1.0.0.0
   * 
   */
  protected static function get_badge($type)
  {
    if (empty($type))
      return '';

    if (self::$badges == null) {
      self::$badges = array(
        'matched' => array(
          'title' => PF_B_Manager::$options['matched_badge']['badge'],
          'color' => PF_B_Manager::$options['matched_badge']['color'],
          'type' => 'matched'
        ),
        'placed' => array(
          'title' => PF_B_Manager::$options['placed_badge']['badge'],
          'color' => PF_B_Manager::$options['placed_badge']['color'],
          'type' => 'placed'
        ),
        'profile in progress' => array(
          'title' => PF_B_Manager::$options['in_progress_badge']['badge'],
          'color' => PF_B_Manager::$options['in_progress_badge']['color'],
          'type' => 'profile in progress'
        ),
        'waiting' => array(
          'title' => PF_B_Manager::$options['waiting_badge']['badge'],
          'color' => PF_B_Manager::$options['waiting_badge']['color'],
          'type' => 'waiting'
        )
      );
    }
    return isset(self::$badges[mb_strtolower($type)]) ? self::$badges[mb_strtolower($type)] : '';
  }

  /**
   * get parent name from string
   * 
   * @param  object  $profile
   * @param  integer $parent  
   * 
   * @return string
   *
   * @since  1.0.0.0
   * 
   */
  protected static function get_name($names, $parent = 1, $delimeter = '')
  {
    $parent -= 1;

    if (empty($names))
      return '';

    $names = explode(' ', $names);
    if (sizeof($names) > 2)
      $names = array($names[0], $names[sizeof($names) - 1]);

    foreach ($names as $key => $name)
      $names[$key] = ucfirst(strtolower($name));

    return isset($names[$parent]) ? ($parent > 0 ? $delimeter : '') . $names[$parent] : '';
  }

  /**
   * create array of filters for specific family
   * 
   * @param  object $profile
   * 
   * @return array
   *
   * @since 1.0.0.0
   * 
   */
  protected static function get_filters_value($profile)
  {
    $fields = array_merge(array_keys(self::$data['filters']), array('religion2'));

    $data = array();
    foreach ($fields as $key) {
      if (isset($profile->$key)) {
        $v = $profile->$key;

        if ($v == '')
          continue;

        if ($key == 'religion2')
          $key = 'religion';

        if ($key == 'kidsInFamily')
          $v = (int)$v == 1 ? __('1 child', PF_B_Manager::$action) : sprintf(__('%s children', PF_B_Manager::$action), $v);

        if ($key == 'religion') {
          if (!isset($data[$key]))
            $data[$key] = array();
          $data[$key][] = $v;
          $data[$key] = array_unique($data[$key]);
        } else
          $data[$key] = $v;

        if (!in_array($v, self::$data['filters'][$key]))
          self::$data['filters'][$key][] = $v;
        unset($v);
      }
    }

    $data['location'] = $profile->country;
    $data['state'] = $profile->country . (isset($profile->state) ? '/' . $profile->state : '');

    return array_unique($data, SORT_REGULAR);
  }

  /**
   * change filters for frontend format
   * 
   * @param  object $filters
   * @param  array  $base
   * 
   * @return array
   *
   * @since 1.0.0.0
   * 
   */
  protected static function filters($filters, $base)
  {
    $data = array();

    foreach ($filters as $key => $options) {
      if (in_array($key, PF_B_Manager::$options['sorting_options'])) {
        $data[] = array(
          'id' => $key,
          'active' => '',
          'placeholder' => $base['captions'][$key],
          'options' => $options,
        );
      }
    }
    return $data;
  }

  /**
   * get all parents
   * 
   * @return array
   *
   * @since 1.0.1.0
   * 
   */
  public static function get_parents_array()
  {
    return self::get_parents() === false ? array() : self::$data['parents']['id'];
  }
} 
