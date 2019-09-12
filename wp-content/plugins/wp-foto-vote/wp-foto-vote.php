<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WP Foto Vote
 * Plugin URI:        https://wp-vote.net/
 * Description:       Simple photo contest plugin with ability to user upload photos. Includes protection from cheating by IP and cookies. User log voting. After the vote invite to share post about contest in Google+, Twitter, Facebook, OK, VKontakte.
 * Version:           2.3.14
 * Author:            Maxim Kaminsky
 * Author URI:        http://www.maxim-kaminsky.com/
 * Text Domain:       fv
 * Domain Path:       /languages
 * Plugin support EMAIL: support@wp-vote.net

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

//define("FV_DISABLE_EVERCOOKIE", 1);

// If this file is called directly, abort.
if (!class_exists('WP')) {
    die();
}

if ( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) {
    die( 'Access denied.' );
}

if ( !defined('FV_ADMIN__COMPETITORS_LIST__FETCH_USER_EMAIL') ) {
    define('FV_ADMIN__COMPETITORS_LIST__FETCH_USER_EMAIL', 1);
}

define('ST_PUBLISHED', '0');
define('ST_MODERAION', 1);
define('ST_MODERATION', 1);
define('ST_DRAFT', 2);
define('FV_RES_OP_PAGE', '15');
define('FV_CONTEST_BLOCK_WIDTH', '200');
define('UPDATE_SERVER_URL', 'https://wp-vote.net/updater/');

define('FV_GET_MAX_POSTS', 2000);
define('FV_GET_MAX_PAGES', 800);

define('FV_DB_VERSION', '1.5.258');
if ( !defined('FV_DB_ENGINE') ) {
    define('FV_DB_ENGINE', 'InnoDB');
}

define('FV_NOTIFICATIONS_VERSION', 5);
define('FV_UPDATE_KEY', 'Dmfsf9ezPnR9WB');

if ( !defined("FV_LOG_FILE") ) {
	define("FV_LOG_FILE", dirname(__FILE__) . '/logs/log.txt');
}
define("FV_ROOT", dirname(__FILE__));

require __DIR__ . '/vendor/autoload.php';

if (!SHORTINIT) {

    /**
     * The code that runs during plugin activation.
     */
    require_once FV_ROOT . '/includes/class-fv-activator.php';

    /**
     * The code that runs during plugin deactivation.
     */
    require_once FV_ROOT . '/includes/class-fv-deactivator.php';

    /** This action is documented in includes/class-wsds-activator.php */
    register_activation_hook( __FILE__, array( 'FV_Activator', 'activate' ) );

    /** This action is documented in includes/class-wsds-deactivator.php */
    register_deactivation_hook( __FILE__, array( 'FV_Deactivator', 'deactivate' ) );
}

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once FV_ROOT . '/includes/class-fv.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.2.073
 */

function run_FV() {
	$plugin = new FV( plugin_basename( __FILE__ ), FV_ROOT );
	$plugin->run();

    // (!defined('DOING_AJAX') || DOING_AJAX == FALSE)
    if ( !SHORTINIT && is_admin() && !defined('FV_DISABLE_UPDATER') ) {
        $FvUpdateChecker = PucFactory::buildUpdateChecker(
            UPDATE_SERVER_URL . '?action=get_metadata&slug=' . FV::SLUG, __FILE__, FV::SLUG
        );

        //Add the license key to query arguments.
        $FvUpdateChecker->addQueryArgFilter('fv_filter_update_checks');
    }
}
run_FV();