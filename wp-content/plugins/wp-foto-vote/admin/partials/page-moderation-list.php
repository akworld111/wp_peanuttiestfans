<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>
    
<div class="wrap fv-page">
    <style type="text/css">
        .ml50 {
            margin-left: 30px;
        }
        .tablenav .actions label {
            display: inline-block;
        }
        .tablenav .actions #fv-filter-contest {
            float: none;
        }

        #table_units {
            background: white;
            border-collapse: collapse;
            width: 100%;
        }

        .postbox {
            padding: 10px;
        }
        #table_units tr{
            border-bottom: 1px solid #CACACA;
        }
        #table_units td{
            padding: 3px 6px 0 6px;
        }

        td.actions {
            min-width: 170px;
            text-align: center;
        }

        td.centered {
            text-align: center;
        }

        .color-red {
            color: red;
        }
    </style>


    <h2><?php _e('Moderation users uploaded photos', 'fv') ?></h2>
    <p>Images will be also deleted from Media library (and hosting) on deleting entry, if you enable option "On deleting contest photo delete image from hosting?" at *Photo contest* => *Settings* => *Additional Tab*.</p>

   <div class="postbox"><div class="inside">
        <?php //fv_render_tpl( FV::$ADMIN_PARTIALS_ROOT . '_table_units_moderation.php', compact('items') ); ?>
           <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
           <form id="competitors-filter" method="get">
               <!-- For plugins, we also need to ensure that the form posts back to our current page -->
               <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

               <!-- Now we can render the completed list table -->
               <?php $listTable->search_box(__('Search data > 1 symbol by', "fv"), 'fv') ?>

               <!-- Now we can render the completed list table -->
               <?php $listTable->display() ?>
           </form>
   </div></div>

        
</div>