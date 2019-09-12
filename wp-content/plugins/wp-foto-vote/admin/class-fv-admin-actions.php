<?php
/**
 * Process admin actions like "Add contest", "Add Form", etc
 *
 * @package    FV
 * @subpackage FV/admin
 * @author     Maxim K <support@wp-vote.net>
 */
class FV_Admin_Actions
{

    /**
     * Process some admin actions (clear log and etc)
     *
     * After redirect on the same page
     * IN ORDER TO if you reboot page action don't not perform again (like clear log)
     *
     * @since    2.2.73
     * in this class since    2.2.500
     */
    public static function process_admin_actions()
    {
        // Avoid access for users, that does not have enough Permissions
        if ( !FvFunctions::curr_user_can() ) {
            return;
        }

        new Test_Notifications();

        if (isset($_REQUEST['action'])) {
            $current_page = isset($_REQUEST['page']) ? sanitize_title($_REQUEST['page']) : '';
            if ('create' == $_REQUEST['action'] && 'fv' == $current_page) {
                $contest_options = array(
                    'name'        => '',
                    'cover_image' => '',
                    'timer'       => isset($_POST['timer']) ? sanitize_text_field($_POST['timer']) : '',
                    'upload_enable' => isset($_POST['upload_enable']) && $_POST['upload_enable'] ? 1 : 0,
                );
                if ( !empty($_POST['name']) ) {
                    $contest_options['name'] = sanitize_text_field($_POST['name']);
                }
                if ( !empty($_POST['cover_image']) ) {
                    $contest_options['cover_image'] = (int)$_POST['cover_image'];
                }

                $contest_id = FV_Admin_Contest::create_contest( $contest_options );

                /**
                 * Create new Page/Post for Contest
                 * @since 2.2.500
                 */
                if ( is_numeric($contest_id) && !empty($_POST['create_post']) ) {
                    $post_params = array(
                        'post_title'    =>  sanitize_text_field( $_POST['post_title'] ),
                        'post_type'     =>  $_POST['post_type'],
                        'post_status'   =>  $_POST['post_status'],
                        'post_content'  =>  "[fv id={$contest_id}]",
                    );
                    if ( $post_params['post_type'] == 'post' ) {
                        $post_params['post_category'] = [ (int)$_POST['cat'] ];
                    }
                    if ( $contest_options['cover_image'] ) {
                        $post_params['_thumbnail_id'] = $contest_options['cover_image'];
                    }

                    FV_Admin_Contest::create_contest_page( $contest_id, $post_params );
                }
                
                do_action( 'fv/admin/after_create_contest', $contest_options, $contest_id );
                
                // Очищаем лог
                wp_safe_redirect(admin_url("admin.php?page={$current_page}&show=config&contest={$contest_id}"));
                die();
                
            } elseif ('fv' == $current_page && 'save' == $_REQUEST['action'] && isset($_POST['contest_title']) ) {
                $contest_id = FV_Admin_Contest::save();
                $contest = new FV_Contest( $contest_id );
                wp_safe_redirect( $contest->getAdminUrl('config', array('saved'=>'true') ) );
                die();
                
            } elseif ('fv' == $current_page && 'fv_reactivate_contest' == $_REQUEST['action'] && isset($_REQUEST['contest_id']) ) {
                if ( !check_admin_referer("fv_reactivate_contest_nonce") ) {
                    die("No secure!");
                }

                $contest = new FV_Contest( $_REQUEST['contest_id'] );
                $contest->date_finish = gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS + 72*HOUR_IN_SECONDS ) ) );
                $contest->status = FV_Contest::PUBLISHED;
                $contest->save();

                // Reset all Winners
                //if ( !$contest->isManualWinnersPick() ) {
                    $contest->resetWinners();
                //}

                wp_add_notice( sprintf(__('Contest "%s" reactivated', 'fv'), $contest->name), "success");

                wp_safe_redirect( $contest->getAdminUrl() );
                do_action( 'shutdown' );
                die();
                
            } elseif ('fv_reset_winners' == $_REQUEST['action'] && isset($_REQUEST['contest_id']) ) {
                
                if ( !check_admin_referer("fv_reset_winners_nonce") ) {
                    die("No secure!");    
                }

                $contest = new FV_Contest( $_REQUEST['contest_id'] );
                //if ( $contest->isManualWinnersPick() ) {
                    $contest->resetWinners();
                //}

                wp_add_notice( sprintf(__('Contest "%s" winners has been reset!', 'fv'), $contest->name), "success");

                wp_safe_redirect( $contest->getAdminUrl('winners') );
                die();
                
            } elseif ('fv_save_contest_description' == $_REQUEST['action'] && isset($_GET['contest']) && isset($_POST['meta_description']) ) {

                if (!check_admin_referer("fv_save_contest_description")) {
                    die("No secure!");
                }

                $contest = new FV_Contest((int)$_REQUEST['contest']);
                $contest->setDescription($_POST['meta_description']);

                wp_add_notice(sprintf(__('Contest "%s" description has been updated!', 'fv'), $contest->name), "success");

                wp_safe_redirect($contest->getAdminUrl('description'));
                die();
            } elseif ('fv_contest_change_categories_state' == $_REQUEST['action'] && !empty($_REQUEST['contest_id']) ) {

                if (!check_admin_referer("fv_contest_change_categories_state_nonce")) {
                    die("No secure!");
                }

                $contest = new FV_Contest((int)$_REQUEST['contest']);
                $categories_on = trim($_REQUEST['categories_on']);
                $contest->categories_on = in_array($categories_on, array('', 'single', 'multi')) ? $categories_on : '';
                $contest->save();

                wp_add_notice(sprintf(__('Contest "%s" categories status has been updated!', 'fv'), $contest->name), "success");

                wp_safe_redirect($contest->getAdminUrl('categories'));
                die();
            } elseif ('clear' == $_REQUEST['action'] && $current_page == 'fv-debug') {
                
                if ( !check_admin_referer("fv_clear_log") ) {
                    die("No secure!");
                }
                // Очищаем лог
                FvLogger::clearLog();
                wp_add_notice(__('Log cleared.', 'fv'), "success");
                //do_action("shutdown");
                wp_safe_redirect(admin_url("admin.php?page={$current_page}&clear=true"));
                die();
                
            } elseif ($current_page == 'fv-settings' && 'reset_database' == $_REQUEST['action']) {
                
                if ( !check_admin_referer("fv_reset_database") ) {
                    return;
                }
                // Очищаем лог
                $my_db = new FV_DB;
                $my_db->clearAllData();
                wp_add_notice(__('Tables cleared.', 'fv'), "success");
                wp_safe_redirect(admin_url("admin.php?page=fv-settings&clear=true"));
                die();
                
            } elseif ('fv_reset_translations' == $_REQUEST['action']) {
                if ( !check_admin_referer("fv_reset_translations") ) {
                    return;
                }                
                fv_reset_public_translation();
                wp_safe_redirect(admin_url("admin.php?page=fv-translation&reset=success"));
                die();
            } elseif ('fv-update-key' == $_REQUEST['action'] && isset($_REQUEST['update-key']) && $current_page == 'fv-license') {
                if ( wp_verify_nonce($_POST['_wpnonce'], 'fv-update-key-nonce') && fv_update_key_and_get_details($_REQUEST['update-key']) ) {
                    wp_safe_redirect(admin_url("admin.php?page=fv-license&updated=true"));
                } else {
                    wp_safe_redirect(admin_url("admin.php?page=fv-license"));
                }
                die();
            } elseif ('fv-clone-form' == $_REQUEST['action'] && !empty($_GET['form'])) {
                if ( !check_admin_referer("fv_clone_form") ) {
                    wp_nonce_ays("fv_clone_form");
                }
                $form_to_clone = ModelForms::q()->findByPK( (int)$_GET['form'] );

                // Remove unnecessary fields
                unset( $form_to_clone->ID );
                unset( $form_to_clone->last_edited );
                unset( $form_to_clone->locked );
                unset( $form_to_clone->created );
                
                $old_title = $form_to_clone->title;
                $form_to_clone->title .= ' Cloned';
                $form_to_clone->is_default = 0;

                ModelForms::q()->insert( (array)$form_to_clone );

                wp_add_notice( sprintf(__('Form "%s" has been cloned.', 'fv'), $old_title), "success");

                wp_safe_redirect(admin_url("admin.php?page=fv-formbuilder"));
                die();
            } elseif ('fv-delete-form' == $_REQUEST['action'] && !empty($_GET['form'])) {
                if ( !check_admin_referer("fv_delete_form") ) {
                    wp_nonce_ays("fv_delete_form");
                }
                ModelForms::q()->delete( (int)$_GET['form'] );
                wp_add_notice(__('Form has been deleted.', 'fv'), "success");
                wp_safe_redirect(admin_url("admin.php?page=fv-formbuilder"));
                die();
            }elseif ('fv-add-form' == $_REQUEST['action']) {
                $new_form_ID = ModelForms::q()->insert(
                    array(
                        'title'                 => 'New form',
                        'type'                  => 'standard',
                        'data_type'             => 'photo',
                        'fields'                => Fv_Form_Helper::get_default_form_structure(),
                        'multiupload'           => 0,
                        'multiupload_captions'  => 'Photo caption',
                        'multiupload_count'     => 3,
                        'skin'                  => 'default',
                    )
                );
                if (!$new_form_ID) {
                    wp_add_notice(__('Troubles with form creating!', 'fv'), "error");
                } else {
                    wp_add_notice(__('Form has been created.', 'fv'), "success");
                }
                wp_safe_redirect(admin_url("admin.php?page=fv-formbuilder&form=$new_form_ID"));
                die();
            }
        }

        if ( FvFunctions::is_ajax() ) {
            return;
        }
/*
        if ( isset($_GET['fv_cron']) ) {
            FV_Admin_Winners::CRON_finish_contests();
            die('fv_cron');
        }
*/

        fv_dismissible_notice(
            'fv-customizer-info',
            sprintf('<strong>WP Foto Vote</strong> :: Did you know about possibility to customize some of Gallery skins in Customizer?
                        <a class="button" href="%s" target="_blank">Try now >></a>',
                admin_url('customize.php?autofocus[panel]=wp_foto_vote')
            ),
            'info'
        );

        $current_wp_theme = wp_get_theme();
        if ( $current_wp_theme->get('Name') == 'Selfie photo contest Theme' && version_compare($current_wp_theme->get('Version'), '1.70') === -1 ) {
            wp_add_notice(
                sprintf('<strong>WP Foto Vote :: Current version of Selfie PC theme (%s) is not compatible with with current version WP Foto Vote (%s). '
                    . 'Please upgrade theme or downgrade plugin!</strong>', $current_wp_theme->get('Version'), FV::VERSION)
                , 'danger'
            );
        }        
        
        // "Maximum File Upload Size" is too small
        if ( wp_max_upload_size() && wp_max_upload_size() < 1000 * 1024 * 4 ) {
            fv_dismissible_notice(
                sprintf('<strong>WP Foto Vote :: "Maximum File Upload Size" directives is set to %s (that is smaller than 4 MB) - so you can have problems with public images upload! Recommended size is 5+ MB.</strong>
                        <br/><a href="%s" target="_blank">More info</a> and <a href="%s" target="_blank">how to increase it</a>.',
                    'http://php.net/manual/en/ini.core.php#ini.upload-max-filesize',
                    size_format( wp_max_upload_size()),
                    'http://www.wpbeginner.com/wp-tutorials/how-to-increase-the-maximum-file-upload-size-in-wordpress/'
                )
                , 'danger'
            );

        }

        // IS "Single photo view" setup correct?
        if ( rand(1, 10) > 4 && fv_setting('single-link-mode', 'mixed') != 'lightbox' ) {

            $single_page_id = fv_setting('single-page');
            if ( empty($single_page_id) ) {
                wp_add_notice(
                    sprintf('<strong>WP Foto Vote :: Single photo page does not selected. Please set up it <a href="%s">here</a> ("Page used for showing single photo?" option)</strong>
                        <br/>Else Single Contest Photo view will not work (like <code>www.site.com/contest-photo/123/</code>)!',
                        admin_url("admin.php?page=fv-settings#single_photo")
                    )
                    , 'danger'
                );
            } else {
                global $wpdb;
                $single_page_post = $wpdb->get_row("SELECT ID,post_status,post_content FROM `{$wpdb->posts}` WHERE ID = '{$single_page_id}';");

                if ( !$single_page_post ) {

                    wp_add_notice(
                        sprintf('<strong>WP Foto Vote :: Single photo page does not exists. Please set up it <a href="%s">here</a> ("Page used for showing single photo?" option)</strong>
                        <br/>Else Single Contest Photo view will not work (like <code>www.site.com/contest-photo/123/</code>)!',
                            admin_url("admin.php?page=fv-settings#single_photo")
                        )
                        , 'danger'
                    );

                } elseif ($single_page_post->post_status == 'trash') {

                    wp_add_notice(
                        sprintf('<strong>WP Foto Vote :: Single photo page move to Trash. Please <a href="%s">restore it</a>!</strong> <br/>
                            Else Single Contest Photo view will not work (like <code>www.site.com/contest-photo/123/</code>)!',
                            admin_url('edit.php?post_status=trash&post_type=page')
                        )
                        , 'danger'
                    );
                }elseif ( strpos($single_page_post->post_content, '[fv') === FALSE ) {

                    wp_add_notice(
                        sprintf('<strong>WP Foto Vote :: "Single photo view" page content does not contain shortcode <code>[fv]</code>. Please <a href="%s">add it</a>!</strong><br/>
                            Else Single Contest Photo view will not work (like <code>www.site.com/contest-photo/123/</code>)!',
                            admin_url("post.php?post={$single_page_id}&action=edit")
                        )
                        , 'danger'
                    );
                }
            }

        }

        // Cache notices
        if ( defined('WP_CACHE') && WP_CACHE ) {
            if ( !fv_setting('cache-support') ) {
                fv_dismissible_notice(
                    'cache-notice',
                    '<strong>WP Foto Vote :: using cache can cause issues with voting.</strong><br/>
                        You can: enable cache support in Photo contest -> Settings (this will disable some Wordpress security features and increase cheating) or remove contest pages from caching 
                        (<a target="_blank" href="https://www.redbridgenet.com/how-to-exclude-specific-pages-from-w3-total-cache/">W3C</a> and <a target="_blank" href="http://support.jawtemplates.com/goodstore/web/?p=491">WP Super Cache</a>).'
                    , 'warning'
                );
            }
        }

        // VK notice
        if ( fv_setting('single-vk-comments') && !fv_setting('vk-app-id') ) {
            wp_add_notice(
                '<strong>WP Foto Vote :: You have enabled Vkotakte comments, but do not configured VK App ID.</strong><br/>
                    Please navigate to Photo contest -> Settings -> Additional and configure.'
                , 'danger'
            );
        }

        // Disqus notice
        if ( fv_setting('single-ds-comments') && !fv_setting('ds-slug') ) {
            wp_add_notice(
                '<strong>WP Foto Vote :: You have enabled Disqus comments, but do not configured Disqus "site shortname", so Disqus widget can\'t be displayed.</strong><br/>
                    Please navigate to Photo contests -> Settings -> <i>Additional tab</i> and configure.'
                , 'danger'
            );
        }
        
        // Check
        //if (fv_photo_in_new_page()) {
        $contests_without_pages = ModelContest::query()
            ->where_null('page_id')
            ->what_field(' `t`.`id`, `t`.`name` ')
            ->find();
        if (count($contests_without_pages) > 0) {
            $contests_without_pages_message = 'WP Foto Vote :: your have contests without selected Page, where it has been placed. Please fix this! (Option : "Page, where contest are placed") <br/>  ';
            $contests_without_pages_message_details = array();
            foreach ($contests_without_pages as $contest_without_page) {
                $contests_without_pages_message_details[] = '<a href="' . admin_url("admin.php?page=fv&show=config&contest={$contest_without_page->id}") . '" target="_blank">' . $contest_without_page->name . '</a>';
            }
            // Add notice without last 2 chars
            wp_add_notice( $contests_without_pages_message . implode(', ',$contests_without_pages_message_details), 'warning');
        }


        $contests_with_social_login = ModelContest::query()
            ->where('voting_security_ext', 'social')
            ->what_fields( array('id', 'name') )
            ->find();

        // TODO - change message
        if ( count($contests_with_social_login) > 0 ) {
//            $social_login_warning = sprintf(
//                'WP Foto Vote :: your have contests with "Simple Social login" Additional voting security. <strong>This feature has <a href="%s" target="_blank">security vulnerability</a>.</strong> List of contests: ',
//                'https://www.facebook.com/WordpressPhotoContestApp/posts/957098634449453');
//            $contests_with_social_login_message_details = array();
//            foreach ($contests_with_social_login as $contest_one) {
//                $contests_with_social_login_message_details[] = '<a href="' . admin_url("admin.php?page=fv&show=config&contest={$contest_one->id}") . '" target="_blank">' . $contest_one->name . '</a>';
//            }
//            wp_add_notice( $social_login_warning. implode(', ',$contests_with_social_login_message_details), 'danger');

            $api_keys_link = admin_url('admin.php?page=fv-settings#api_keys');

            if ( fv_setting('voting-slogin-fb') && ( !get_option('fotov-fb-apikey') || ! get_option('fv-fb-secret') ) ) {
                wp_add_notice(
                    sprintf('You have enabled Simple Social login but haven\'t configured <a href="%s">Facebook api keys</a>>!', $api_keys_link)
                , 'danger');
            }

            if ( fv_setting('voting-slogin-vk') && ( !fv_setting('vk-app-id') || !fv_setting('vk-app-secret') ) ) {
                wp_add_notice(
                    sprintf('You have enabled Simple Social login but haven\'t configured <a href="%s">VK api keys</a>>!', $api_keys_link)
                , 'danger');
            }

        }

        if ( function_exists("is_notification_defined") && !is_notification_defined("fv/contest/to-user/verify-email") ) {
            // Add this notification
            FV_Notifications_Core::install( true );
        }
    }
}