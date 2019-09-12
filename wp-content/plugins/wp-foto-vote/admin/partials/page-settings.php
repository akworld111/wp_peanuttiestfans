<?php
defined('ABSPATH') or die("No script kiddies please!");

$settings_tabs = array(
    "general" => __("General", 'fv'),
    "single_photo" => __("Single", 'fv'),
	"toolbar" => __("Toolbar", 'fv'),
	"voting" => __("Voting", 'fv'),
	"upload" => __("Upload", 'fv'),
	"sharing" => __("Social / Sharing", 'fv'),
	"winners" => __("Winners", 'fv'),
	"leaders" => __("Leaders", 'fv'),
    "email_notify" => __("Email notify", 'fv'),
    "api_keys" => __("API keys", 'fv'),
    "additional" => __("Additional", 'fv'),
    "gdpr" => __("GDPR / Privacy tweaks", 'fv'),
    "custom_js" => __("Custom Javascript", 'fv'),
);

?>

<div class="wrap fv-page" id="fv-setting-page">
	<h2>WP Foto Vote settings <small>Blue circled tooltip boxes are important</small></h2>

	<?php
	if (isset($_REQUEST['clear'])) {
		echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>' .
		__('Data deleted. Reactivate plugin!', 'fv') .
		'</strong></p></div>';
	}

	?>
    <div id="fv-settings-updated" class="updated settings-error" style="display: none;">
        <p>
            <strong><?php _e('Configuration saved.', 'fv') ?></strong>
        </p>
    </div>

    <div class="fv_content_wrapper">

		<form method="post" action="options.php" id="fv_settings-form" onsubmit="fv_save_settings(this); return false;">
			<?php settings_fields('fotov-settings-group'); ?>

			<div class="fv-tabs" id="" data-activate-trigger-on="true">
				<!-- Tabs -->
				<nav class="fv-tabs-nav">
					<ul class="fv-tabs-navigation">
						<?php foreach ($settings_tabs as $group_slug => $group_name) : ?>
							<li><a href="#<?php echo $group_slug; ?>" data-content="<?php echo $group_slug; ?>" class="<?php echo ($group_slug == 'general') ? 'selected' : ''; ?>">
									<?php echo $group_name; ?>
								</a></li>
						<?php endforeach; ?>
					</ul> <!-- fv-tabs-navigation -->

					<div class="fv-settings-tabs-extra-blocks">

						<div class="postbox sidebar-addons">
							<?php
							$ad_sidebar_id = rand(1, 4);
							?>
							<h3>
								<span><?php _e('Addons', 'fv') ?></span>
							</h3>
							<div class="inside">
								<a href="http://wp-vote.net/ad_sidebar-<?php echo $ad_sidebar_id; ?>" target="_blank">
									<img src="<?php echo '//wp-vote.net/show/ad_sidebar-'  . $ad_sidebar_id . '.png'; ?>" alt="addons"/>
								</a>
							</div>
						</div>

						<div class="postbox">
							<?php
							$defaults = array('valid' => 0, 'expiration' => 'Key not entered!');
							$key = get_option('fv-update-key', '');
							$key_details = get_option('fv-update-key-details', $defaults);
							?>

							<h3>
								<span><?php _e('Update status', 'fv') ?> <small>(curr. version <?php echo FV::VERSION ?>)</small></span>
							</h3>
							<div class="inside">
								<div class="gadash-title">
									<a href="#" target="_blank">
										<img width="32" src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/update.png') ?>" >
									</a>
								</div>
								<div class="gadash-desc">
									<strong><?php _e('Key status: ', 'fv') ?></strong>
									<?php echo isset($key_details['status']) ? fv_get_update_key_status_as_text($key_details['status']) : 'not set'; ?>

									<?php if ( isset($key_details['status']) && $key_details['status'] == 4 ):
										?>
										<a href="http://wp-vote.net/extending-a-license/?license_key=<?php echo $key; ?>" target="_blank">Extend license >></a>
										<?php
									endif;
									?>
									<br/><strong><?php _e('Expiration: ', 'fv') ?></strong>
									<?php echo isset($key_details['expiration']) ? $key_details['expiration'] : 'not set'; ?>
									<br/><?php echo ($key)? __('<strong>Key</strong>: ', 'fv') . $key : __('Key not entered!', 'fv'); ?>
									<a href="<?php echo admin_url("admin.php?page=fv-license"); ?>">edit</a>
								</div>
							</div>
						</div>
					</div>
				</nav>

				<div class="fv-tabs-content">
					<!-- Tabs content / Generate ul list with tables for tabbed navigation -->
					<ul >
						<?php foreach ($settings_tabs as $group_slug => $group_name) : ?>
							<li data-content="<?php echo $group_slug; ?>" class="<?php echo ($group_slug == 'general') ? 'selected' : ''; ?>">
								<?php require_once "settings/_tab_{$group_slug}.php"; ?>
							</li>
						<?php endforeach; ?>
					</ul>


					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save all Changes', 'fv') ?>" />
						<span class="spinner"></span>
					</p>

				</div>

			</div>  <!-- .fv-tabs :: END -->

			<?php if (current_user_can('install_plugins')): ?>
				<a onclick="return confirm('<?php _e('Are you sure to delete all contests & photos & votes data from database?', 'fv') ?>');" href="<?php echo wp_nonce_url( admin_url('admin.php?page=fv-settings&action=reset_database'), 'fv_reset_database' ); ?>"><?php _e('Clear all plugin data in database.', 'fv') ?></a>
			<?php endif; ?>
		</form>


	</div>  <!-- .fv_content_wrapper :: END -->

	<style type="text/css">
        h2 small {
            font-size: 12px;
            color: #2ea2cc;
        }

		td.socials span {
			width: 120px;
			display: inline-block;
		}
		td.upload-additionals span {
			width: 110px;
			display: inline-block;
		}

        td.colorpicker {
            line-height: 35px;
        }

        .important .box .dashicons {
            color: #2ea2cc;
        }

		.box {
			width: 25px;
			float: right;
			height: 100%;
		}
		.tooltip {
			width: 25px;
		}
		.no-padding, .no-padding td {
			padding: 0;        
		}
		.no-padding h3 {
			margin: 5px 0;
			padding: 0 0 0 10px;
		}
		.dashicons-info:before {
			content: "\f348";
		}

		.fv-settings-tabs-extra-blocks{
			margin-top: 20px;
		}
		.fv-settings-tabs-extra-blocks .postbox{
			min-width: 200px;
		}
		.fv-settings-tabs-extra-blocks h3 {
			margin: 0.5em 0.5em;
		}
		@media (max-width: 996px) {
			.fv-settings-tabs-extra-blocks .postbox{
				display: none;
			}
		}

	</style>

</div>