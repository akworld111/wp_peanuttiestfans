<div class="fv_wrapper">
    <label><div class="number"><?php echo $counter; ?></div> <?php echo stripslashes($this->_get_opt('title')); ?> </label>
    <select name="category_contest_id" class="form-control" id="category-contest-id" onchange="fv_changed_contest(this);" required>
        <?php 
        if ( !$active_default ):
            echo '<option value="">'. $this->_get_opt('empty') .'</option>';
        endif;
       
        foreach ($contests as $contest) :
            printf('<option value="%s" %s>%s</option>', 
                   $contest->id, 
                   ($active_default && $current_contest->id == $contest->id) ? 'selected' : '', 
                   $contest->name
                );
        endforeach;
        ?>
    </select>
    <span class="description"><?php echo stripslashes($this->_get_opt('description')); ?></span>
</div>