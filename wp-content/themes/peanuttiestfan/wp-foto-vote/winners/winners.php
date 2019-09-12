<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * @version 1.0
 * @since 2.2.503
 * @created 25.03.2017
 * @last_modified 25.03.2017
 */

/** @var array              $template_data */
/** @var FV_Competitor[]    $winners */
/** @var string             $heading */
?>
<div class="FV_Winners">

    <div class="FV_Winners__list">
        <?php
        if ( count($winners) ) :
            foreach ($winners as $winner) {
                $template_data['competitor'] = $winner;

                FV_Templater::render(
                    FV_Templater::locate($template_data['skin'], 'winner_item.php', 'winners'),
                    $template_data,
                    false,
                    'winner_one'
                );
            }
        else:
            echo '<h4><i class="fvicon fvicon-trophy"></i> ', fv_get_transl_msg('winners_not_picked_heading'),'</h4>';
        endif;
        ?>
    </div>
   
</div>
