<?php

/**
 * Abstract Competitor Class
 *
 * @version  2.2.500
 * @package  FV/Abstracts
 * @category Abstract Class
 * @author   Maxim K
 */

/**
 * Class FV_Competitor
 */
class FV_Competitor extends FV_Abstract_Object implements FV_Competitor_ABS
{
    const PUBLISHED     = 0;
    const MODERATION    = 1;
    const DRAFT         = 2;

    const CATEGORY_START_ID = 990000000000000;
    /**
     * @var FV_Competitor_ABS
     */
    protected $object;

    protected $terms;
    
    /**
     * @param int|object    $object         Contest or init.
     * @param bool          $from_cache
     * @param bool          $get_meta       Preload meta
     * @param bool|array    $meta_data      Used by ModelCompetitors::find when prefetch Meta
     *
     * @return FV_Competitor
     */
    public function __construct( $object = 0, $from_cache = false, $get_meta = false, $meta_data = false )
    {
        $this->model = ModelCompetitors::q();
        parent::__construct($object, $from_cache);

        if ( isset($this->object->id) && !empty($this->object->contest_id) ) {
            $this->meta = new FV_Competitor_Meta( $this->object->id, $meta_data, $get_meta, $this->object->contest_id );
        }

        $this->properties['author_avatar_id'] = 'virtual';
        $this->properties['author_avatar'] = 'virtual';
        $this->properties['author_link'] = 'virtual';
        $this->properties['author_url'] = 'virtual';
        $this->properties['author_name'] = 'virtual';
        $this->properties['author_nicename'] = 'virtual';
        $this->properties['author_avatar_html'] = 'virtual';
        $this->properties['wp_user_email'] = 'virtual';
        $this->properties['author_user_email'] = 'virtual';

        return $this;
    }

    protected function _meta_instance() {
        return new FV_Competitor_Meta($this->object->id, false, false, $this->object->contest_id);
    }

    /**
     * Magic __get method for get contest params
     *
     * @param  string $key Key name.
     * @return mixed
     */
    public function __get( $key ) {
        if ( !$this->object ) {
            return false;
        }

        if ( $key == 'options' && !is_array($this->object->options) ) {
            if ( $this->object->options ) {
                $this->object->options = maybe_unserialize($this->object->options);
                return $this->object->options;
            } else {
                return $this->object->options = array();
            }
        }

        return parent::__get($key);
    }

    ###### add >> getContestOptionsArr


    /**
     * Used in template files (single_item.php, list_item.php, etc)
     * for retrieve photo Heading with using template from settings
     *
     * #FILTER :: apply_filters('fv/public/competitor/get_tpl_heading', $HEADING, $type, $this)
     *
     * <code>
     * echo $competitor->getHeadingForTpl('list');
     * // or
     * echo $competitor->getHeadingForTpl('single');
     * </code>
     *
     * @param string            $type           'list'|'single'|'winner'
     * @param null|object       $contest
     * @param mixed             $head_tpl       #since 2.2.804
     *
     * @return string
     */
    public function getHeadingForTpl($type = 'list', $contest = null, $head_tpl = false) {
        if ( !$head_tpl ) {
            $head_tpl = fv_setting($type.'-head-tpl', '{name}');
        }

        $HEADING = str_replace(
            array('{ID}', '{name}', '{description}', '{link_to_single}'),
            array($this->object->id, $this->object->name, $this->object->description, $this->getSingleViewLink()),
            $head_tpl
        );

        $HEADING = $this->_tplReplaceMetaTags($HEADING, 'head-'.$type);

        ## Process '{contest_name}' in single photo page
        if ( $type != 'list' && $contest ) {
            $HEADING = str_replace('{contest_name}', $contest->name, $HEADING);
        }

        $HEADING = stripslashes($HEADING);

        return apply_filters('fv/public/competitor/get_tpl_heading', $HEADING, $type, $this);
    }

    /**
     * Used in template files (single_item.php, list_item.php, etc)
     * for retrieve photo Description with using template from settings
     *
     * #FILTER :: apply_filters('fv/public/competitor/get_tpl_heading', $HEADING, $type, $this)
     *
     * <code>
     * echo $competitor->getDescForTpl('list');
     * // or
     * echo $competitor->getDescForTpl('single');
     * </code>
     *
     * @param string            $type           'list'|'single'|'winner'*
     * @param mixed             $desc_tpl       #since 2.2.804
     *
     * @return string
     */
    public function getDescForTpl($type = 'list', $desc_tpl = false) {

        if ( !$desc_tpl ) {
            $desc_tpl = fv_setting($type.'-desc-tpl', '{description}');
        }

        // ##### Conditional TAGS #####
        // @since 2.2.800
        if ( !$conditional_matches = wp_cache_get('tpl_contestant_desc-conditionals-'.$type, 'fv') ) {
            preg_match_all('/\[IF \{([^\}]*)\}\](.[^\]]+)(?:\[ELSE\](.+?))?\[ENDIF\]/s', $desc_tpl, $conditional_matches);
            wp_cache_add('tpl_contestant_desc-conditionals-'.$type, $conditional_matches, 'fv');
        }
        
        //fv_dump($conditional_matches);

        // PROCESS [[IF {name}]]{name} is cool[[ELSE]]no name[[ENDIF]]
        // OR [[IF {name}]]{name} is cool[[ENDIF]]
        if ( !empty($conditional_matches) ) {
            $math_tag = '';

            foreach ( $conditional_matches[0] as $m_index => $conditional_match )
            {
                $math_tag = trim($conditional_matches[1][$m_index]);
                $math_tag_value= '';

                // Find a value of TAG
                if ( strpos($math_tag, 'meta_') !== false ) {
                    $math_tag_value = $this->meta()->get_value( str_replace('meta_', '', $math_tag) );
                } elseif ( in_array('name', 'description', 'full_description') ) {
                    $math_tag_value = $this->object->$math_tag;
                }

                // IF $math_tag_value not empty - use it, else check ELSE or remove all
                if ( $math_tag_value ) {
                    // IF value is not empty
                    $desc_tpl = str_replace($conditional_match, $conditional_matches[2][$m_index], $desc_tpl);
                } elseif( empty($math_tag_value) && $conditional_matches[3][$m_index] ) {
                    // ELSE
                    $desc_tpl = str_replace($conditional_match, $conditional_matches[3][$m_index], $desc_tpl);
                } else {
                    // IF NO ELSE condition - REMOVE ALL
                    $desc_tpl = str_replace($conditional_match, '', $desc_tpl);
                }
            }
        }
        // ##### END :: Conditional TAGS #####

        $DESC = str_replace(
            array('{ID}', '{name}', '{description}', '{full_description}', '{link_to_single}'),
            array($this->object->id, $this->object->name, $this->object->description, $this->object->full_description, $this->getSingleViewLink()),
            $desc_tpl
        );

        $DESC = $this->_tplReplaceMetaTags($DESC, 'desc-'.$type);
        $DESC = $this->_tplReplaceCategoryTags($DESC);
        $DESC = stripslashes($DESC);

        return apply_filters('fv/public/competitor/get_tpl_desc', $DESC, $type, $this);
    }

    /**
     * Generate Lightbox title from TPL
     *
     * @return string
     *
     * @since 2.2.807
     */
    public function getLightboxTitleForTpl() {
        $tpl = fv_setting( 'lightbox-title-format' );

        $title = str_replace(
            array('{ID}', '{name}', '{description}', '{full_description}','{link_to_single}'),
            array(
                $this->object->id,
                htmlspecialchars(stripslashes($this->object->name)),
                htmlspecialchars(stripslashes($this->object->description)),
                htmlspecialchars(stripslashes($this->object->full_description)),
                $this->getSingleViewLink()
            ),
            $tpl
        );

        if ( strpos($title, '{votes}') !== false ) {
            // If hide votes count enabled
            if ( !$this->getContest(true)->isNeedHideVotes() ) {
                $title = str_replace('{votes}', fv_get_transl_msg('vote_count_text') . ": <span class='sv_votes_{$this->id}'>" . $this->getVotes() . '</span>', $title);
            } else {
                $title = str_replace('{votes}', '', $title);
            }
        }

        $title = $this->_tplReplaceMetaTags($title, 'lightbox');

        return apply_filters('fv/public/competitor/get_tpl_lightbox', $title, 'lightbox', $this);
    }

    /**
     * @param $tpl
     * @return mixed
     *
     * @since 2.2.807
     */
    public function _tplReplaceCategoryTags( $tpl ) {
        if ( strpos($tpl, '{categories_comma_separated}') !== false ) {
            $tpl = str_replace( '{categories_comma_separated}', $this->getCategories( 'string' ), $tpl );
        }
        if ( strpos($tpl, '{category_first}') !== false ) {
            $categories = $this->getCategories();
            if ( $categories ) {
                $tpl = str_replace('{category_first}', $categories[0]->name, $tpl);
            } else {
                $tpl = str_replace('{category_first}', '', $tpl);
            }
        }

        return $tpl;
    }

    /**
     * @param string    $tpl
     * @param string    $cache_key
     *
     * @return mixed
     * @throws Exception
     * @since 2.2.807
     */
    public function _tplReplaceMetaTags( $tpl, $cache_key ) {
        
        if ( strpos($tpl, '{meta_') !== false ) {
            if ( !$matches = wp_cache_get('tpl_contestant_'.$cache_key, 'fv') ) {
                preg_match_all ( '^{meta_([A-Za-z0-9_-]+)}^', $tpl, $matches);
                wp_cache_add('tpl_contestant_'.$cache_key, $matches, 'fv');
            }

            if ( !empty($matches) ) {
                $all_meta = $this->meta()->get_all_keyed();
                $replace_to = '';
                foreach ($matches[1] as $meta_id => $meta_key) {
                    if ( isset($all_meta[$meta_key]) ) {
                        $replace_to = $all_meta[$meta_key]->value;
                    } else {
                        $replace_to = '';
                    }

                    $tpl = str_replace( $matches[0][$meta_id], $replace_to, $tpl);
                }
            }
        }
        return $tpl;
    }

    /**
     * Return link to competitor page
     *
     * <code>
     * <a href="<?php echo esc_url($competitor->getSingleViewLink()); ?>">view</a>
     * </code>
     *
     * @since   2.2.500
     * @return  string				    URL http://test.com/contest_photo/123/
     */
    public function getSingleViewLink() {
        return fv_single_photo_link( $this->object->id, false, $this->object->contest_id );
    }

    /**
     * Return link to Admin page
     *
     * @since   2.2.608
     * @return  string				    URL /wp-admin/admin.php?page=fv&show=competitors&contest=1&order=DESC&orderby=id&paged=1&s=90&search_where=id
     */
    public function getAdminLink() {
        return admin_url('admin.php?page=fv&show=competitors&contest=' . $this->contest_id . '&search_where=id&s=' . $this->id);
    }

    /**
     * @param bool $cached
     *
     * @return FV_Contest
     */
    public function getContest($cached = true) {
        return new FV_Contest($this->object->contest_id, $cached);
    }

    /**
     * Get competitor Votes count (or rating)
     *
     * <code>
     * echo $competitor->getVotes();
     * // or
     * echo $competitor->getVotes( $contest );
     * </code>
     *
     * @param object|bool       $contest
     * @param string|bool       $contest_voting_type    'rate'|'rate_summary'|'like'
     * @param string|bool       $rating_format          false|'total_count'|'only_numbers'
     *
     * @return string
     */
    public function getVotes($contest = false, $contest_voting_type = false, $rating_format = false) {
        if (!$contest && !$contest_voting_type) {
            $contest = $this->getContest();
            $contest_voting_type = $contest->voting_type;
        } elseif ($contest) {
            $contest_voting_type = $contest->voting_type;
        }

        $result = '';
        if ( $contest_voting_type == 'rate' ) {
            if ( !$rating_format ) {
                $result = round($this->object->votes_average, 1) . ' ' . fv_get_transl_msg('vote_rating_delimiter', 'of') . ' ' . fv_setting('rate-stars-count', 5);
            } elseif ( $rating_format == 'total_count' ) {
                $result = round($this->object->votes_average, 1) . ' ' . fv_get_transl_msg('vote_rating_delimiter', 'of') . ' ' . fv_setting('rate-stars-count', 5) . ' (' . $this->object->votes_count . ')';
            } elseif ( $rating_format == 'only_numbers' ) {
                $result = round($this->object->votes_average, 1);
            }
        } elseif ( $contest_voting_type == 'rate_summary' ) {
            if ( !$rating_format || $rating_format == 'only_numbers' ) {
                $result = round($this->rating_summary, 1);
            } elseif ( $rating_format == 'total_count' ) {
                $result = round($this->rating_summary, 1) . ' (' . $this->votes_count . ')';
            }
        } else {
            $result = $this->object->votes_count;
        }

        /**
         * @since 2.2.800
         * 
         * Filter 'fv/public/competitor/get_author_name'
         *
         * @param FV_Competitor $this
         * @param string|int    $result
         * @param string        $contest_voting_type    'vote' or 'rate'
         * @param FV_Contest    $contest
         */
        return apply_filters('fv/competitor/get_votes', $result, $this, $contest_voting_type, $contest);
    }

    /**
     * Get uploader user name (if uploaded by registered user)
     *
     * @return string
     */
    public function getAuthorName() {
        if ( !$this->user_id ) {
            return;
        }
        
        if ( $this->author_name ) {
            return $this->author_name;
        }

        $author = get_userdata($this->user_id);
        if ( $author ) {
            $this->author_name = $author->display_name;
        }

        $this->author_name = apply_filters('fv/competitor/get_author_name', $this->author_name, $author, $this);

        return $this->author_name;
    }

    /**
     * @return string
     */
    public function getAuthorLink() {
        if ( !$this->user_id ) {
            return;
        }

        if ( $this->author_link ) {
            return $this->author_link;
        }

        if ( function_exists("um_user_profile_url") ) {
            $this->author_link = um_user_profile_url($this->user_id);
        } elseif ( !function_exists("bp_core_get_user_domain") ) {
            $this->author_link = get_author_posts_url( $this->user_id );
        } else {
            $this->author_link = get_author_posts_url( $this->user_id );
        }

        $this->author_link = apply_filters('fv/competitor/get_author_link', $this->author_link, $this);

        return $this->author_link;
    }

    /**
     * @param int       $size
     * @return string
     */
    public function getAuthorAvatarHtml( $size = 50 ) {
        if ( !get_option('fv-display-author-avatar', false) ) {
            return apply_filters('fv/competitor/get_author_avatar_icon', '<i class="fvicon fvicon-user-circle-o author-avatar-icon"></i>', $this);
        }

        if ( !$this->user_id ) {
            return;
        }

        if ( function_exists('um_get_user_avatar_url') ) {
            $um_ser_avatar_data = um_get_user_avatar_data($this->user_id, $size);

            // esc_attr( $um_ser_avatar_data['class'] )

            $this->author_avatar_html = sprintf( '<img src="%s" class="%s" width="%s" height="%s" alt="%s" data-default="%s"/>',
                esc_attr( $um_ser_avatar_data['url'] ),
                'contest-block__author-avatar',
                esc_attr( $um_ser_avatar_data['size'] ),
                esc_attr( $um_ser_avatar_data['size'] ),
                esc_attr( $um_ser_avatar_data['alt'] ),
                esc_attr( $um_ser_avatar_data['default'] )
            );
            
        } elseif ( function_exists('bp_core_fetch_avatar') ) {
            $this->author_avatar_html = bp_core_fetch_avatar( array('item_id'=>$this->user_id, 'width'=>$size, 'height'=>$size) );
        } else {
            $this->author_avatar_html = get_avatar( $this->user_id, $size );
        }

        $this->author_avatar_html = apply_filters('fv/competitor/get_author_avatar_html', $this->author_avatar_html, $this);

        return $this->author_avatar_html;
    }

    /**
     * Get src of competitor original image
     *
     * <code>
     * echo $competitor->getImageUrl();
     * // or
     * <img src="<?php echo esc_url($competitor->getImageUrl()); ?>" atl="<?php echo esc_attr($competitor->name); ?>"/>
     * </code>
     *
     * @return string
     */
    public function getImageUrl() {
        return apply_filters('fv/get_photo_full', $this->object->url, $this);
    }


    /**
     * Do not used
     *
     * @return string
     */
    public function getImageArr() {

        $image_array = apply_filters('fv/get_photo_full_arr/custom', array(), $this);

        // If have correct Thumb data
        if ( $image_array && !is_wp_error($image_array) && count($image_array) >= 3 ) {
            return $image_array;
        }

        $full_url = '';

        if ( !empty($this->object->image_id) ) {
            if ( !$this->isVideo() ) {
                $full_url = wp_get_attachment_image_src( $this->object->image_id, 'full' );
                if ( is_array($full_url) ) {
                    $full_url = $full_url[0];
                }
            } else {
                $full_url = wp_get_attachment_url( $this->object->image_id );

            }
        } else {
            $full_url = $this->object->url;
        }
//
//        if ( empty($this->object->image_id) && !empty($this->object->url) ) {
//            // FIX for Instagram addon, if disabled
//            if ( isset($this->options['width']) && isset($this->options['height']) ){
//                $proportion_w = $thumb_size['width'] / $this->options['width'];
//                return array( $this->url_min, round($this->options['width']*$proportion_w), round($this->options['height']*$proportion_w), 'ig');
//            }
//            // FIX for S3 addon, if disabled
//            if ( isset($this->options['thumb_w']) && isset($this->options['thumb_h']) ){
//                $proportion_w = $thumb_size['width'] / $this->options['thumb_w'];
//                return array( $this->url_min, round($this->options['thumb_w']*$proportion_w), round($this->options['thumb_h']*$proportion_w), 's3');
//            }
//
//            // Fix if no Image ID is set but URL set
//            return array(
//                esc_url($this->object->url),
//                get_option('fotov-image-width', 330),
//                get_option('fotov-image-width', 330),
//                false
//            );
//        } elseif( empty($this->object->image_id) && empty($this->object->url) ) {
//            // Fix if no Image ID and URL is set
//            return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
//        }
        
        return array(
            apply_filters('fv/get_photo_full', $full_url, $this),
            330,
            530
        );
    }    

    /**
     * Return thumbnail SRC array, similar to wp_get_attachment_image_src()
     *
     * <code>
     * $thumb = $competitor->getThumbArr();
     * printf('<img src="%s" width="%d" height="%d" atl="%s"/>', esc_url($thumb[0]), esc_attr($thumb[1]), esc_url(esc_attr[2]), esc_attr($competitor->name));
     * // or
     * printf('<img src="%s" width="%d" height="%d" atl="%s"/>', esc_url($thumb[0]), esc_attr($thumb[1]), esc_url(esc_attr[2]), esc_attr($competitor->getHeadingForTpl()));
     * </code>
     *
     * @param bool|array $thumb_size
     * @param bool       $get_icon_for_non_photo
     * array {
         'width'     => 220,
         'height'    => 220,
         'crop'      => true,
         'size_name' => 'fv-thumb',
       }
     * @return array
     */
    public function getThumbArr( $thumb_size = array(), $get_icon_for_non_photo = false ) {

        if ( $thumb_size != 'full' || $thumb_size == 'thumbnail' || !is_array($thumb_size) ) {
            $thumb_size = wp_parse_args($thumb_size, array(
                'width'     => get_option('fotov-image-width', 300),
                'height'    => get_option('fotov-image-height', 300),
                'crop'      => get_option('fotov-image-hardcrop', false) == '' ? false : true,
                'size_name' => 'fv-thumb',
            ));            
        }

        $thumb_size = apply_filters('fv/get_photo_thumbnail/thumb_size', $thumb_size, $this);

        $thumb_array = apply_filters('fv/get_photo_thumbnail/custom', array(), $this, $thumb_size);

        // If have correct Thumb data
        if ( $thumb_array && !is_wp_error($thumb_array) && count($thumb_array) >= 3 ) {
            return $thumb_array;
        }

        if ( $thumb_size == 'full' ) {
            return $this->getImageArr();
        }
        
        if ( empty($this->object->image_id) && !empty($this->object->url) ) {
            // FIX for Instagram addon, if disabled
            if ( $this->storage == 'ig' ){
                $thumb_side = $thumb_size['width'] < 640 ? $thumb_size['width'] : 640;
                return array( $this->url_min, $thumb_side, $thumb_side, 'ig');
            }
            // FIX for Cloudinary addon, if disabled
            if ( isset($this->options['width']) && isset($this->options['height']) ){
                $proportion_w = $thumb_size['width'] / $this->options['width'];
                return array( $this->url_min ? $this->url_min : $this->url, round($this->options['width']*$proportion_w), round($this->options['height']*$proportion_w), 'cloudinary');
            }
            // FIX for S3 addon, if disabled
            if ( isset($this->options['thumb_w']) && isset($this->options['thumb_h']) ){
                $proportion_w = $thumb_size['width'] / $this->options['thumb_w'];
                return array( $this->url_min, round($this->options['thumb_w']*$proportion_w), round($this->options['thumb_h']*$proportion_w), 's3');
            }
            // Fix if no Image ID is set but URL set
            return array(
                esc_url($this->object->url),
                get_option('fotov-image-width', 330),
                get_option('fotov-image-width', 330),
                false
            );
        } elseif( empty($this->object->image_id) && empty($this->object->url) ) {
            // Fix if no Image ID and URL is set
            return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
        }
        
        // Check If Jetpack is Active
        if ( FvFunctions::$Jetpack_photon_active === null ) {
            FvFunctions::$Jetpack_photon_active = class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' );
        }

        if ( $get_icon_for_non_photo && $this->isLocalVideo() ) {
            return wp_get_attachment_image_src( $this->object->image_id, array($thumb_size['width'], $thumb_size['height']), true );
        }



        // If Jetpack is Active - use it
        // ->storage    > fix for Youtube/Vimeo videos
        if ( FvFunctions::$Jetpack_photon_active && !$this->storage ) {
            $photonImgSrc = Jetpack_PostImages::fit_image_url($this->object->url, $thumb_size['width'], $thumb_size['height'] );
            return array( $photonImgSrc, $thumb_size['width'], $thumb_size['height'] );
        }elseif ( fv_setting('thumb-retrieving', 'plugin_default') == 'plugin_default' ) {
            // Getting an attachment image with multiple parameters
            return FvFunctions::image_downsize( $this->object->image_id, $thumb_size, $this->object->url );
        } else {

//            if ( $this->isLocalVideo() ) {
//                $res = wp_get_attachment_image_src( $this->object->image_id, array($thumb_size['width'], $thumb_size['height']), true );
//            } else {
                $res = wp_get_attachment_image_src( $this->object->image_id, array($thumb_size['width'], $thumb_size['height']) );
            //}

            if ( $res === false ) {
                return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
            }
            return $res;
        }
   
    }

    /**
     * Return thumbnail SRC url, similar to wp_get_attachment_image_src()
     *
     * <code>
     * printf('<img src="%s" width="200" atl="%s"/>', esc_url($competitor->getThumbUrl()), esc_attr($competitor->name));
     * // or
     * printf('<img src="%s" atl="%s"/>', esc_url($competitor->getThumbUrl()), esc_attr($competitor->name));
     * // or get with custom size
     * printf('<img src="%s" atl="%s" width="250"/>',
     *  esc_url($competitor->getThumbUrl(array('width'=>250, 'height'=>0, 'crop'=>0, 'size_name'=>'fv_250w'))),
     *  esc_attr($competitor->name)
     * );
     * </code>
     *
     * @param bool|array $thumb_size
     * array {
        'width'     => 220,
        'height'    => 220,
        'crop'      => true,
        'size_name' => 'fv-thumb',
     }
     * @return array
     */
    public function getThumbUrl($thumb_size = false) {
        $thumb_arr = $this->getThumbArr($thumb_size);
        if ( is_array($thumb_arr) ) {
            return $thumb_arr[0];
        }
        return '';
    }

    /**
     * @return bool
     * 
     * @since 2.2.801
     */
    public function isVideo() {
        if ( isset($this->mime_type) && FALSE !== strpos($this->mime_type, 'video/')  ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     *
     * @since 2.2.801
     */
    public function isLocalVideo() {
        if ( ! $this->storage && $this->isVideo()  ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     *
     * @since 2.3.05
     */
    public function isPublished() {
        if ( $this->status != self::PUBLISHED ) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     *
     * @since 2.3.05
     */
    public function isOnModeration() {
        if ( $this->status == self::MODERATION ) {
            return true;
        }
        return false;
    }

    /**
     * Return thumbnail SRC url, similar to wp_get_attachment_image_src()
     *
     * @return array {
            'height'    => int,
            'width'    => int,
            'filesize'      => float,
        }
     */
    public function getAttachmentDetails() {
        if ( !$this->image_id ) {
            return false;
        }

        $att_details = array(
            'height'   => 0,
            'width'    => 0,
            'file_size' => 0,
        );

        $meta = wp_get_attachment_metadata( $this->image_id );

        if ( isset($meta['height']) && isset($meta['width']) ) {
            $att_details['height'] = $meta['height'];
            $att_details['width']  = $meta['width'];
        }

        if ( isset( $meta['file_size'] ) ) {

            $att_details['file_size'] = $meta['filesize'];

        } elseif ( $attached_file = get_attached_file( $this->image_id ) ) {
            if ( file_exists( $attached_file ) ) {
                $att_details['file_size'] = filesize($attached_file);
            }
        }

        if ( $att_details['file_size'] ) {
            $att_details['file_size'] = size_format( $att_details['file_size'] );
        }

        return $att_details;
    }

    /**
     * Get translated winner place cation
     * Example: "1 place"
     *
     * @param int|string    $place
     * @return string
     */
    public function getPlaceCaption($place = '') {
        return apply_filters('fv/public/competitor/get_place_caption',
            $this->place_caption ? $this->place_caption : ($this->place ? $this->place : $place ) . ' ' . fv_get_transl_msg('winners_place', 'place'),
            $place,     // CAN BE EMPTY
            $this
        );
    }

    /**
     * @param bool    $force      Force to fetch Email via get_userdata() if not queried
     * @return string Email
     */
    public function getAuthorEmail($force = false) {
        if ( !$user_email = $this->user_email ) {
            if( isset($this->object->wp_user_email) ) {
                $user_email = $this->object->wp_user_email;
            } elseif ( $force && $this->user_id ){
                $user = get_userdata( $this->user_id );
                $user_email = $user->user_email;
            }
        }

        return apply_filters('fv/competitor/get_user_email',
            $user_email,
            $this
        );
    }

    /**
     *
     * @since   2.2.800
     *
     * @param string    $format             "objects"/"IDs"/"slugs"/"string"
     * @param string    $stringDelimiter
     * @param bool      $cached             Return from Cache?
     *
     * @return array
     * [[
        [term_id]     => 162
        [name]        => Test
        [slug]        => test
        [term_group]  => 0
        [term_taxonomy_id] => 170
        [taxonomy]    => fv-category
        [description] =>
        [parent]      => 0
        [count]       => 2
     * ], etc]

     */
    public function getCategories( $format = 'objects', $stringDelimiter = ', ', $cached = true ) {
        if ( !$this->terms || !$cached ) {
            $this->terms = wp_get_object_terms($this->getEmulatedWpObjectID(), FV_Competitor_Categories::$tax_slug);
        }

        if ( !$this->terms ) {
            if ( $format != 'string' ) {
                return array();
            } else {
                return '';
            }
        }

        switch ($format){
            case 'objects':
                return $this->terms;
            case 'IDs':
                return array_map(function($term) {
                    return $term->term_id;
                }, $this->terms);
            case 'slugs':
                return array_map(function($term) {
                    return $term->slug;
                }, $this->terms);
            case 'string':
                return implode($stringDelimiter, array_map(function($term) {
                    return $term->name;
                }, $this->terms) );
        }
    }

    /**
     *
     * @since   2.2.806
     *
     * @param array $ID_or_Slugs     IDs/slugs
     * @param string $type           "IDs"/"slugs"
     *
     * @usage
     * <code>
     * $competitor->hasCategories( array(1,4,7) );
     * or
     * $competitor->hasCategories( "cats" );
     * </code>
     *
     * @return array
     */
    public function hasCategories( $ID_or_Slugs, $type = 'IDs' ) {
        if ( !is_array($ID_or_Slugs) ) {
            $ID_or_Slugs = array($ID_or_Slugs);
        }

        $exists_cats = $this->getCategories( $type );

        return array_intersect( $exists_cats, $ID_or_Slugs );
    }

    /**
     *
     * @since   2.2.800
     *
     * @param array $terms_arr  Photo Categories
     *
     * @return int
     */
    public function _setCategoriesCache( $terms_arr ) {
        $this->terms = $terms_arr;
    }

    /**
     *
     * @since   2.2.800
     *
     * @param array $new_terms_arr  Array of ID's or Slug's
     * @param bool  $append         True - add cats, False - remove old, add new
     *
     * @return int
     */
    public function setCategories( $new_terms_arr, $append = true ) {
        return wp_set_object_terms( $this->getEmulatedWpObjectID(), $new_terms_arr, FV_Competitor_Categories::$tax_slug, $append );
    }

    /**
     * @since   2.2.800
     * @return  bool
     */
    public function resetCategories(  ) {
        return wp_remove_object_terms( $this->getEmulatedWpObjectID(), $this->getCategories('IDs'), FV_Competitor_Categories::$tax_slug );
    }

    /**
     * @since   2.2.800
     *
     * Generate big INT for emulate unique POST id
     *
     * @return int
     */
    public function getEmulatedWpObjectID () {
        //$start_ID = 990000000000000;
        //          123456789012345
        // of max   18446744073709551615

        return self::CATEGORY_START_ID + $this->id;
    }

    /**
     * Delete competitor from database
     *
     * @since 2.2.500
     *
     * @param bool      $send_notify        Do we need call mail notify? (set False if mass delete with Contest)
     * @param string    $admin_comment      Text comment, that admin can add while approving photo on Moderation page
     *
     * @return bool
     */
    public function delete( $send_notify = true, $admin_comment = '' ) {
        do_action('fv/delete_photo', $this);

        // To leave Contestant
        //ModelCompetitors::query()->update( array( 'status'=> ST_DRAFT ), $this->object->id );

        if ( $send_notify ) {
            FV_Notifier::sendCompetitorNotificationToUser( 'fv/competitor/to-user/deleted', $this, $admin_comment );
            //FV_Notifier::sendCompetitorDeleted( $this, $admin_comment );
        }

        $is_deleted = ModelCompetitors::query()->delete($this->object->id);

        // delete Contestant + may be Image from hosting
        if ($this->object && $is_deleted && get_option('fv-image-delete-from-hosting', false)) {
            // in not registered some hooks
            if ( apply_filters('fv/admin/delete_photo_attachment/custom', false, $this) === false ) {
                if ( $this->image_id ) {
                    wp_delete_attachment($this->image_id, true);
                }
            }
        }


        // Reset Categories to Decrease Category Counter
        if ( $this->object ) {
            $this->resetCategories();
        }

        if ( $is_deleted ) {
            // Fetch Meta, to use in hook before delete all
            $this->meta()->get_all();
            
            ## Delete meta
            ModelMeta::q()->deleteByContestantID($this->object->id);
            ## Delete votes fom Log
            ModelVotes::q()->deleteByContestantID($this->object->id);

            do_action('fv/delete_photo/ready', $this);
        } else {
            do_action('fv/delete_photo/fail', $this);
        }

        return $is_deleted;
    }

    /**
     * Set competitor status "Published" from "On Moderation"
     * Also send email to user, if $send_notify is True
     *
     * <code>
     * $competitor->approve( true );
     * </code>
     *
     * @param bool      $send_notify        Do we need call mail notify?
     * @param string    $admin_comment      Text comment, that admin can add while approving photo on Moderation page
     *
     * @return bool
     */
    public function approve( $send_notify = true, $admin_comment = '' ) {

        $this->status = self::PUBLISHED;
        $is_saved = $this->save();
        //ModelCompetitors::query()->updateByPK(array('status' => ST_PUBLISHED), $this->object->id);

        do_action('fv/approve_photo', $this->object->id, $admin_comment);

        if ( $send_notify ) {

            FV_Notifier::sendCompetitorNotificationToUser( 'fv/competitor/to-user/approved', $this, $admin_comment );
            //FV_Notifier::sendCompetitorApproved( $this, $admin_comment );
        }

        return $is_saved;
    }

    /**
     * Rotate image and thumbnails
     *
     * @param integer       $angle
     *
     * @return array|bool
     *
     * @private
     *
     * @since      2.2.609
     */
    public function rotateImage( $angle, $detailed_res )
    {
        $result = false;
        $message = '';
        $data = array('competitor_id'=>$this->id);

        try {

            if ( false === apply_filters('fv/admin/rotate_image/custom', false, $this, $angle) && $this->image_id ) {

                /* Get the image source, width, height, and whether it's intermediate. */
                $image_path = get_attached_file( $this->image_id );

                $WP_Image_Editor = wp_get_image_editor( $image_path, array() );

                if ( $WP_Image_Editor->rotate($angle) === true ) {

                    $WP_Image_Editor->save($image_path);
                    $attach_data = wp_generate_attachment_metadata( $this->image_id, $image_path );

                    // TODO - check
                    $meta = wp_get_attachment_metadata( $this->image_id );
                    # Code from https://developer.wordpress.org/reference/functions/wp_delete_attachment/#source
                    // Remove intermediate and backup images if there are any.
                    if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
                        foreach ( $meta['sizes'] as $size => $sizeinfo ) {
                            $intermediate_file = str_replace( basename( $image_path ), $sizeinfo['file'], $image_path );
                            /** This filter is documented in wp-includes/functions.php */
                            $intermediate_file = apply_filters( 'wp_delete_file', $intermediate_file );
                            //var_dump( path_join(dirname($image_path), $intermediate_file) );
                            @ unlink( path_join(dirname($image_path), $intermediate_file) );
                        }
                    }

                    wp_update_attachment_metadata( $this->image_id,  $attach_data );

                    FvLogger::addLog("rotate_image - rotated success > " . $angle, $image_path);
                    $result = true;
                    $message = 'Rotate_image - rotated success';
                    $data['new_src'] = $this->getThumbUrl();
                } else {
                    $message = 'Rotate_image - error rotate';
                    FvLogger::addLog("rotate_image - error rotate");
                }
            } else {
                FvLogger::addLog('Rotate_image - error - no Image ID', $this);
                $message = 'Rotate_image - error - no Image ID';
            }

        } catch(Exception $ex) {
            FvLogger::addLog( "rotate_image - some error ", $ex->getMessage() );
            $message = $ex->getMessage();
        }

        if ( $detailed_res ) {
            return compact('result', 'message', 'data');
        } else {
            return $result;
        }
    }
}