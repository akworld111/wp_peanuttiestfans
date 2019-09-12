<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * http://codyhouse.co/gem/responsive-tabbed-navigation/
*/
?>

<div class="wrap fv-page" id="fv_translation">
    <?php
    if ($saved) {
		echo '<div id="setting-error-settings_updated" class="updated settings-error">
                <p>
                    <strong>' . __('Translation saved.', 'fv') . '</strong>
                </p>
             </div>';
	}
	?>    

	<h2>
        <?php _e('Translating messages', 'fv') ?>
    </h2>

    <div class="notice notice-info notification-notice">
        <p>
            Please help translate plugin - submit your translation and it will be integrated to the next plugin release! <a class="button button-secondary" href="<?= admin_url("admin.php?page=fv-translation&submit-translation=true"); ?>">Submit >></a>
        </p>
    </div>

    <?php _e('Please don\'t use double quotes (") !', 'fv') ?><br/>
    <strong><?php printf( __('To translate form fields go to <a href="%s" target="_blank">"Form Builder"</a> page!', 'fv'), admin_url('admin.php?page=fv-formbuilder') )?></strong>


	<form name="fv-translation" method="POST">
        <div class="fv-tabs">
            <!-- Tabs -->
            <nav class="fv-tabs-nav">
                <ul class="fv-tabs-navigation">
                    <?php foreach ($key_groups as $group_name => $group_fields) : ?>
                        <li>
                            <?php if( empty($group_fields['is_heading']) ): ?>
                                <a href="#0" data-content="<?php echo $group_name; ?>" class="<?php echo ($group_name == 'general') ? 'selected' : ''; ?>">
                                    <?php echo $group_fields['tab_title']; ?>
                                </a>
                            <?php else: ?>
                                <h3><?php echo $group_fields['tab_title']; ?></h3>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul> <!-- fv-tabs-navigation -->
            </nav>

            <div class="fv-tabs-content">
                <!-- Tabs content / Generate ul list with tables for tabbed navigation -->
                <ul >
                    <?php foreach ($key_groups as $group_name => $group_fields) : ?>
                        <li data-content="<?php echo $group_name; ?>" class="<?php echo ($group_name == 'general') ? 'selected' : ''; ?>">
                            <table class="form-table">
                                <?php foreach ($group_fields as $key => $title) : ?>
                                    <tr valign="top">
                                        <?php if ($key == 'tab_title'): ?>
                                            <!-- Tab title -->
                                            <td><h3 class="no_margin"><?php echo $title ?></h3></td>
                                            <td><hr></td>
                                        <?php elseif ( strpos($key,'tab_subtitle') !== false ): ?>
                                            <!-- Tab subtitle -->
                                            <td><hr></td>
                                            <td><h4 class="no_margin tab_subtitle"><?php echo $title ?></h4><hr></td>
                                        <?php elseif ($key == 'tab_description'):
                                            $title = explode('||', $title);
                                            ?>
                                            <!-- Tab description -->
                                            <td><?php echo (is_array($title)&& count($title)==2) ? $title[0] : '<hr>'; ?></td>
                                            <td><span class="no_margin tab_description"><?php echo (is_array($title)&& count($title)==2) ? $title[1] : $title; ?></span></td>
                                        <?php else: ?>
                                            <!-- Tab fields -->
                                            <th scope="row"><?php echo $title ?>: </th>
                                            <td>
                                                <?php if ( !in_array($key, fv_get_public_translation_textareas()) ): ?>
                                                    <input name="<?php echo $key ?>" value="<?php echo ( isset($messages[$key]) ) ? esc_attr(stripcslashes($messages[$key])) : ''; ?>" class="large-text" type="text"/>
                                                <?php else: ?>
                                                    <textarea name="<?php echo $key ?>" class="large-text" rows="4"/><?php echo ( isset($messages[$key]) ) ?  wp_kses_post(stripcslashes($messages[$key])) : ''; ?></textarea>
                                                    <small><?php _e('Common html tags are allowed.', 'fv') ?></small>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <p class="submit">
                    <input type="hidden" name="action" value="save" />
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'fv') ?>" />

                    <?php if (current_user_can('install_plugins')): ?>
                        &nbsp;&nbsp;&nbsp;<a onclick="return confirm('<?php _e('Are you sure to reset all translations?', 'fv') ?>');" href="<?php echo wp_nonce_url( admin_url('admin.php?page=fv-translation&action=fv_reset_translations'), 'fv_reset_translations' ); ?>"><?php _e('Reset all translations?', 'fv') ?></a>
                    <?php endif; ?>
                </p>
            </div>

        </div>


    </form>

	<style>
		table td input {
			min-width: 60%;
		}

		.no_margin {
			margin: 0;
		}

        .tab_subtitle {
            text-align: left;
            font-size: 1.1em;
            float: left;
            margin-right: 18px;
        }

	</style>    
</div>