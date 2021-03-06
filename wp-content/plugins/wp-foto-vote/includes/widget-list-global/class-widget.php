<?php
/**
 * List style widget
 *
 * @since       2.2
 *
 * @package     wp-foto-vote
 * @subpackage  includes/widget-gallery
 * @author      Maxim Kaminsky <support@wp-vote.net>*
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

class Widget_FV_List_Global extends WP_Widget
{

    /**
     * Unique identifier for your widget.
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @var      string
     */
    protected $widget_slug = 'widget-fv-list-global';

    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct()
    {
        parent::__construct(
            $this->get_widget_slug(),
            __('Photo contest - global list', $this->get_widget_slug()),
            array(
                'classname' => $this->get_widget_slug() . '-class',
                'description' => __('Shows list photos from all contests.', $this->get_widget_slug())
            )
        );

        add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

    } // end constructor


    /**
     * Return the widget slug.
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug()
    {
        return $this->widget_slug;
    }

    /*--------------------------------------------------*/
    /* Widget API Functions
    /*--------------------------------------------------*/

    /**
     * Outputs the content of the widget.
     *
     * @param array args  The array of form elements
     * @param array instance The current instance of the widget
     * @return int|void
     */
    public function widget($args, $instance)
    {

        wp_enqueue_style($this->get_widget_slug() . '-widget-styles', FV::$ASSETS_URL . 'css/fv_widget.css' );

        // Check if there is a cached output
        $cache = wp_cache_get($this->get_widget_slug(), 'widget');
        //$cache = '';

        if (!is_array($cache))
            $cache = array();

        if (!isset ($args['widget_id']))
            $args['widget_id'] = $this->id;

        if (isset ($cache[$args['widget_id']]))
            return print $cache[$args['widget_id']];

        // go on with your widget logic, put everything into a string and …


        //extract($args, EXTR_SKIP);

        $widget_string = $args['before_widget'];

        // TODO: Here is where you manipulate your widget's values based on their input fields
        ob_start();
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Photo contest', 'fv') : $instance['title']);
        $shows_count = empty($instance['shows_count']) ? '3' : (int)$instance['shows_count'];
        $shows_sort = empty($instance['shows_sort']) ? 'popular' : sanitize_text_field($instance['shows_sort']);
        $show_photo = (bool)$instance['show_photo'];
        $show_photo_size = empty($instance['show_photo_size']) ? '30' : (int)$instance['show_photo_size'];
        //$design = empty($instance['design']) ? 'default' : sanitize_text_field($instance['design']);

        $competitorsQ = ModelCompetitors::query()
            ->where( 'status', ST_PUBLISHED )
            ->set_sort_by_type( $shows_sort )
            ->limit( $shows_count );

        $competitors = $competitorsQ->find();

        $widget_string .= fv_render_tpl(
            FV::$INCLUDES_ROOT . 'widget-list-global/views/widget.php',
            compact('args','competitors','show_photo_size','title','show_photo'),
            true
        );

        $widget_string .= $args['after_widget'];

        $cache[$args['widget_id']] = $widget_string;

        // CACHE FOR 2 HOURS
        wp_cache_set($this->get_widget_slug(), $cache, 'widget', 2 * HOUR_IN_SECONDS);

        print $widget_string;

    } // end widget


    public function flush_widget_cache()
    {
        wp_cache_delete($this->get_widget_slug(), 'widget');
    }

    /**
     * Processes the widget's options to be saved.
     *
     * @param array new_instance The new instance of values to be generated via the update.
     * @param array old_instance The previous instance of values before the update.
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $this->flush_widget_cache();

        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['shows_count'] = (int)$new_instance['shows_count'];
        $instance['shows_sort'] = strip_tags($new_instance['shows_sort']);
        $instance['show_photo'] = $new_instance['show_photo'] ? true : false;
        $instance['show_photo_size'] = (int)$new_instance['show_photo_size'];
        //$instance['design'] = strip_tags($new_instance['design']);

        return $instance;

    } // end widget

    /**
     * Generates the administration form for the widget.
     *
     * @param array instance The array of keys and values for the widget.
     * @return void
     */
    public function form($instance)
    {

        // TODO: Define default values for your variables
        $instance = wp_parse_args((array)$instance,
            array(
                'title' => __('Contest', 'fv'),
                'shows_count' => '3',
                'shows_sort' => 'popular',
                'show_photo' => true,
                'show_photo_size' => '50',
                'design' => 'default'
            )
        );
        // TODO: Store the values of the widget in their own variable
        foreach ($instance as $key => $value) {
            $instance[$key] = esc_attr($value);
        }
        // exract array into varibales
        extract($instance, EXTR_SKIP);

        $contest_list = ModelContest::query()->find();
        // Display the admin form
        include plugin_dir_path(__FILE__) . 'views/admin.php';

    } // end form


} // end class

