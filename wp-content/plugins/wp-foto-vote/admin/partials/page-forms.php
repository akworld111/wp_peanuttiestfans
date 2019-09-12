<div class="wrap">
    <h2>
        <?php _e('Manage upload forms', 'fv') ?>
        <a href="?page=<?php echo $_REQUEST['page']; ?>&action=fv-add-form" class="add-new-h2"><?php echo __('Add new form', 'fv'); ?> </a>
    </h2>

    <table class="widefat fv-forms-table">
        <tr><thead>
            <th>ID</th>
            <th class="fv-forms-table--title"><?php _e('Title', 'fv'); ?></th>
            <th class="fv-forms-table--last-edited"><?php _e('Last Edited', 'fv'); ?></th>
            <th class="fv-forms-table--created"><?php _e('Created', 'fv'); ?></th>
            <th class="fv-forms-table--usedin"><?php _e('Used In', 'fv'); ?></th>
            <th class="fv-forms-table--actions"><?php _e('Actions', 'fv'); ?></th>
        </thead></tr>
        <tbody>
        <?php if ( !empty($forms) ): foreach ($forms as $form): ?>
        <tr>
            <td><?php echo $form->ID; ?></td>
            <td><?php echo $form->title; ?></td>
            <td><?php echo ($form->last_edited)? date('Y-m-d H:i:s',$form->last_edited) : '' ; ?></td>
            <td><?php echo $form->created; ?></td>
            <td><?php echo isset($contests_by_forms[$form->ID]) ? implode(', ', $contests_by_forms[$form->ID]) : ''; ?></td>
            <td>
                <a href="<?php echo admin_url('admin.php?page=fv-formbuilder&form='.$form->ID); ?>"><span class="dashicons dashicons-edit"></span><?php _e('Edit', 'fv'); ?></a>
                <?php if (!$form->is_default): ?>
                    | <a href="<?php echo wp_nonce_url( admin_url('admin.php?action=fv-delete-form&form='.$form->ID), 'fv_delete_form' ); ?>" onclick="return confirm('Are you sure to delete Form?');">
                        <span class="dashicons dashicons-trash"></span> <?php _e('Delete', 'fv'); ?>
                    </a>
                <?php endif; ?>
                | <a href="<?php echo wp_nonce_url( admin_url('admin.php?action=fv-clone-form&form='.$form->ID), 'fv_clone_form' ); ?>">
                    <span class="dashicons dashicons-welcome-add-page"></span><?php _e('Clone', 'fv'); ?></a>
            </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <style>
        .widefat th {
            font-weight: bold;
        }
        .fv-forms-table--actions{
            min-width: 180px;;
        }
    </style>
</div>