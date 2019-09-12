<?php
/** @var FV_Competitor[] $winners */
?>
<div class="metabox-holder">
    <div class="postbox">
            <h3 class="hndle"><span><?php echo __('Winners', 'fv') ?></span></h3>
            <div class="inside b-wrap" id="WinnersLayout">

                <div class="WinnersLayout_main grid grid-pad js-winners-layout-main">
                    <?php if ( $contest->isFinished() && count($winners) ):  ?>
                        <h3><?php _e('Contest winners', 'fv'); ?></h3>
                    <?php elseif ( $contest->isFinished() && !count($winners) && !$contest->getCompetitorsCount() ): ?>
                        <h3><?php _e('Need select contest winners, but competitors count = 0', 'fv'); ?></h3>
                    <?php elseif ( $contest->isFinished() && !count($winners) ): ?>
                        <h3><?php _e('Select contest winners', 'fv'); ?> (<?php echo $contest->winners_count; ?>)</h3>
                    <?php else: ?>
                        <h3><?php echo $contest->winners_count; ?> <?php _e('top most voted competitors', 'fv'); ?></h3>
                    <?php endif; ?>

                    <?php if ( count($winners) ): ?>
                        <div class="row">
                            <?php
                            $N = 1;
                            foreach($winners as $entry): ?>
                            <div class="col-md-5 WinnerOne" data-id="<?php echo $entry->id; ?>">
                                <a href="<?php echo $entry->getSingleViewLink(); ?>">
                                    <div class="WinnerOne__bg" style="background-image: url(<?php echo $entry->getThumbUrl(); ?>);"></div>
                                </a>
                                <div class="text-center"><?php echo $entry->getHeadingForTpl('winner'); ?></div>
                                <div class="text-center"><?php echo $entry->getVotes($contest, false, 'total_count'); ?> <i class="fvicon fvicon-heart" aria-hidden="true"></i></div>
                                
                                <div class="text-center"><i class="fvicon fvicon-trophy2" aria-hidden="true"></i> <strong><?php echo $entry->getPlaceCaption($N) ?></strong></div>
                            </div> <!-- /.col-1-5 -->
                            <?php $N++;
                            endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="clear clear_fix"></div>
                    <br/>

                    <?php if ( !$contest->isFinished() ): ?>
                        <br/>
                        <h4 class="">
                            <?php _e( sprintf('When contest will be finished winners will be picked <strong class="text-underline">"%s"</strong> within max 30 minutes.', $contest->getWinnersPickTitle()), 'fv'); ?>
                        </h4>
                    <?php elseif ( count($winners) ): ?>
                        <h4 class="">
                            <?php _e('Picked', 'fv'); ?>
                            <strong class="text-underline">"<?php echo $contest->getWinnersPickTitle() ?>"</strong>
                        </h4>
                    <?php endif; ?>


                    <div class="clear clear_fix"></div>

                    <div class="WinnersManualPick">
                        <?php
                        /**
                            Если конкурс Завершен && Ручной выбор И
                            = Пустые победители --}}
                            = НеПустые победители, но их меньше чем должно быть
                        */
                        ?>
                        <?php if (
                            $contest->isFinished() && $contest->isManualWinnersPick()
                            && ( !$winners || ( count($winners) < $contest->winners_count && $contest->getCompetitorsCount() > count($winners) ) )
                        ): ?>
                            <br/>
                            <?php for($N=1; $N <= $contest->winners_count; $N++): ?>
                                <?php if ( isset($winners[$N]) ) { continue; } ?>
                                <button type="button" class="button button-primary button-large"
                                        data-call="Winners.manualPick"
                                        data-for-place="<?php echo $N; ?>"
                                        data-action-url="<?php echo wp_nonce_url( admin_url('admin-ajax.php?action=fv_winners_get_entries&contest_id='.$contest->ID), 'fv-winners-get-entries'); ?>">
                                    <?php _e('Pick a winner', 'fv'); ?> <?php echo $N; ?>
                                </button>
                            <?php endfor; ?>
                        <?php endif; ?>

                        <?php if ( $contest->isManualWinnersPick() && count($winners) ) : ?>
                            <form action="<?php echo admin_url(); ?>">
                                <button type="submit" class="button button-primary button-large">
                                    <?php _e('Reset All Winners', 'fv'); ?>
                                </button>
                                <input type="hidden" name="contest_id" value="<?php echo $contest->id; ?>">
                                <input type="hidden" name="action" value="fv_reset_winners">
                                <?php wp_nonce_field('fv_reset_winners_nonce'); ?>
                            </form>
                        <?php endif; ?>
                    </div>
                </div> <!-- /.grid /.grid-pad -->

                <div class="WinnersManualPick_form_wrap js-winner-manual-pick-form-wrap grid grid-pad">
                </div>

            </div>
    </div>
</div>



<script type="text/html" id="tmpl-winners-manual-pick-form">
    <form class="FormHorizontal clear_fix bs-wrap" data-call="Winners.manualPickSubmit" action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST">

        <h3><i class="fa fa-trophy"></i> Pick a winner № {{data.place}}</h3>

        <div class="clear_fix form-group row">
            <label class="col-md-4">Select competitor:</label>
            <div class="col-md-9">
                <select name="competitor_id" class="select2el" >
                    <option value="">== Pick a Winner ==</option>
                </select>
            </div>
        </div><!-- .row :: END -->

        <div class="pedit_separator"></div>


        <div class="clear_fix form-group row">
            <label class="col-md-4" for="place_caption">Winner Caption:</label>
            <div class="col-md-7">
                <input type="text" name="place_caption" id="place_caption" value="{{data.place}}th Place" class="form-control" disabled>
            </div>
            <div class="col-md-3">
                <a href="#edit" class="allow-edit-input-a" data-call="Core.unlockInput" data-target="#place_caption"><i class="fvicon-pencil"></i></a>
            </div>

        </div><!-- .row :: END -->
        <input type="hidden" name="place" value="{{data.place}}">

        <input type="hidden" name="contest_id" value="<?php echo $contest->id; ?>">
        <input type="hidden" name="action" value="fv_winners_process_manual_pick">
        <?php wp_nonce_field('fv_winners_do_manual_pick_nonce'); ?>

        <button type="submit" class="button button-primary button-large">Ready</button>
        <button type="button" class="button button-secondary button-large" data-call="Winners.manualPickCancel">Cancel</button>
    </form>
</script>

<style>
    .select2-thumb {
        height: 24px;
    }

    .select2-thumb-wrap {
        display: inline-block;
        min-width: 45px;
    }

    .select2el{
        width: 95%;
        max-width: 100%;
        display: block;
        box-sizing: border-box;
        position: relative;
    }
</style>

