<?php

/**
 * Multiple checkbox customize control class.
 *
 * @since  2.3.05
 * @access public
 */
class FV_Customize_Control_Select extends WP_Customize_Control {

    /**
     * The type of customize control being rendered.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $type = 'select-html';

    /**
     * Displays the control content.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function render_content() {

        if ( empty( $this->choices ) )
            return;
        $input_id = '_customize-input-' . $this->id;
        $description_id = '_customize-description-' . $this->id;
        $describedby_attr = ( ! empty( $this->description ) ) ? ' aria-describedby="' . esc_attr( $description_id ) . '" ' : '';

        ?>
        <?php if ( ! empty( $this->label ) ) : ?>
            <label for="<?php echo esc_attr( $input_id ); ?>" class="customize-control-title"><?php echo esc_html( $this->label ); ?></label>
        <?php endif; ?>
        <?php if ( ! empty( $this->description ) ) : ?>
            <span id="<?php echo esc_attr( $description_id ); ?>" class="description customize-control-description"><?php echo $this->description; ?></span>
        <?php endif; ?>

        <select id="<?php echo esc_attr( $input_id ); ?>" <?php echo $describedby_attr; ?> <?php $this->link(); ?>>
            <?php
            foreach ( $this->choices as $value => $label ) {
                echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . htmlspecialchars_decode ($label) . '</option>';
            }
            ?>
        </select>

    <?php }

    static function sanitize( $value ) {
        return sanitize_text_field($value);
    }
}