<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Class FV_Public_Countdown
 * @since    2.2.704
 * @before   IN FV_Public class
 */
class FV_Public_Countdown {

    /**
     * Show shortcode countdown by Contest
     *
     * @param array              $atts
     * @param FV_Contest|bool    $contest
     *
     * @return string       Html code
     */
    public static function render_shortcode($atts, $contest = false)
    {

        $atts = wp_parse_args($atts, array(
            'contest_id' => false,
            'type'       => '',
            'count_to'   => 'upload',       // ''upload', 'voting'
        ));

        // Stop if called from [fv] and countdown disabled in contest settings
        if ($contest && $contest->timer == 'no') {
            return;
        }

        if ( !$contest ) {
            if ( !(int)$atts['contest_id'] ) {
                return 'Countdown :: Invalid CONTEST_ID attribute, must be like [fv_countdown contest_id="1"]!';
            }else {
                $contest = ModelContest::query()->findByPK((int)$atts['contest_id'], true);
            }
        }

        if ( !is_object($contest) ) {
            return "Countdown :: Invalid contest ID!";
        }

        if ( empty($atts['type']) ) {
            if ( $contest->timer != 'no' ) {
                $atts['type'] = $contest->timer;
            } else {
                $atts['type'] = 'final';
            }
        }

        if ( !in_array($atts['count_to'], array('upload', 'voting')) ) {
            $atts['count_to'] = 'upload';
        }

        ob_start();

        do_action( 'fv/load_countdown/' . sanitize_title($atts['type']), $contest, $atts );

        $output = ob_get_clean();

        if ( fv_setting('remove-newline', false) ) {
            $output = str_replace( array("\r\n","\n","\r"), "", $output );
        }

        return $output;
    }

}