<?php
/**
 * Variables:
 *
 * $photos - array {
 * [0] = >
     * {
     *      'id' => 1,
     *      'sizes' => {
     *          'thumbnail' => {
     *              height: 150
     *              orientation: "landscape"
     *              url: "http://wp.vote/wp-content/uploads/2012/12/vpuh_261-150x150.jpg"
     *              width: 150
     *          },
     *          'full' => {***}
     *       },

     * }
 * }
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="fv_popup_label">
        <?php echo __('Adding photos:', 'fv') ?>
        <small><?php echo __('Please don\'t use " (double quotes) in fields', 'fv') ?></small>
    </h4>
</div>

<div class="modal-body">

    <div class="photos_list b-wrap">
        %COMPETITORS_FORMS%
    </div>
    <div class="clearfix"></div>
</div>

<div class="modal-footer">
    <div class="buttons">
        <a class="button" data-call="Competitor.saveCompetitors"><?php echo __('Save', 'fv') ?></a>
    </div>
</div>
