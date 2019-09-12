<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * http://codyhouse.co/gem/responsive-tabbed-navigation/
*/
?>

<div class="wrap fv-page" id="fv_submit_translation">

    <h2>
        <?php _e('Translating messages - submit', 'fv') ?>
    </h2>


    <form class="fv-submit-translation-form" method="POST" action="https://wp-vote.net/submit-translation/">
        Site url:
        <input name="site_url" value="<?php echo site_url(); ?>" class="large-text" type="url" readonly>
        <br/>
        Locale:
        <input name="locale" value="<?php echo get_locale(); ?>" class="large-text" type="text" readonly>
        <br/>
        Plugin version:
        <input name="plugin_version" value="<?php echo FV::VERSION; ?>" class="large-text" type="text" readonly>
        <br/>
        Contact email (we can contact with you in case any questions):
        <input name="admin_email" value="<?php echo get_option("admin_email"); ?>" class="large-text" type="email">
        <br/>

        Strings:<br>
<textarea name="messages" readonly="readonly" class="full-textarea">
<?php echo json_encode($messages); ?>
</textarea>

        <button class="button button-primary">Submit</button>
        <input name="v" value="1" type="hidden" readonly>

        <br/><br/>No other data will be submitted!
    </form>

    <style>
        .full-textarea {
            font-family: monospace;
            width: 100%;
            margin: 0;
            height: 300px;
            padding: 10px 20px;
            -moz-border-radius: 0;
            -webkit-border-radius: 0;
            border-radius: 0;
            resize: none;
            font-size: 12px;
            line-height: 20px;
            outline: 0;
        }

    </style>
</div>