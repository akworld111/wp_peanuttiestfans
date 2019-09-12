<?php

/**
 * Class ModelForms
 * Created: 20.10.2016
 */
class ModelForms extends FvQuery
{

    /**
     * Returns the static query of the specified class.
     * @return ModelForms the static query class
     */
    public static function query($className = __CLASS__)
    {
        $class = new $className();
        return $class->set_primary_key('ID');
    }

    /**
     * Returns the static query of the specified class.
     * @return ModelForms the static query class
     */
    public static function q($className = __CLASS__)
    {
        $class = new $className();
        return $class->set_primary_key('ID');
    }

    public function tableName()
    {
        global $wpdb;
        return $wpdb->prefix . "fv_forms";
    }

    public function fields()
    {
        return array(
            'ID'            => '%d',
            'is_default'       => '%d',                 // INT: 0,1
            'title'         => '%s',                    // string
            'type'          => '%s',                    // standard, drag&drop
            'data_type'     => '%s',                    // photo, video, text
            'fields'        => '%s',                    // JSON
            'multiupload'   => '%d',                    // bool
            'multiupload_captions'      => '%s',        // bool
            'multiupload_count'         => '%d',        // INT
            'skin'          => '%s',                    // string
            'locked'        => '%s',                    // string, ex. "4,234234324324324" - user_id,time
            'last_edited'   => '%d',                    // TIMESTAMP int
            'created'       => '%d',                    // TIMESTAMP
        );
    }

    public function getDefaultFormID()
    {
        $form = ModelForms::q()
            ->where('is_default', 1)
            ->what_field('ID')
            ->findRow();
        return $form->ID;
    }

    public function install()
    {
        $sql = "CREATE TABLE " . $this->tableName() . " (
                ID int(7) NOT NULL AUTO_INCREMENT,
                is_default int(1),
                title VARCHAR( 255 ),
                type VARCHAR( 10 ),
                data_type VARCHAR( 15 ),
                fields TEXT NOT NULL,
                multiupload int(2),
                multiupload_captions VARCHAR( 100 ),
                multiupload_count int(7),
                skin VARCHAR( 100 ) NOT NULL,
                locked VARCHAR( 15 ),
                last_edited int(10),    
                created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,    
                PRIMARY KEY  (ID)
                ) ENGINE=" .FV_DB_ENGINE . " DEFAULT CHARSET=utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        FvLogger::checkDbErrors();

        if ( ModelForms::q()->find(true) == 0 ) {
            $form_structure = get_option( 'fv-form-fields' );
            if ( !empty($form_structure) ) {
                $form_structure_obj = json_decode($form_structure);
                $form_structure = json_encode($form_structure_obj->fields);
            } else {
                $form_structure = Fv_Form_Helper::get_default_form_structure();
            }
            fv_log('fv-form-fields backup', $form_structure);
            delete_option( 'fv-form-fields' );
            ModelForms::q()->insert(
                array(
                    'is_default'               => 1,
                    'title'                 => 'Default',
                    'type'                  => 'standard',
                    'data_type'             => 'photo',
                    'fields'                => $form_structure,
                    'multiupload'           => 0,
                    'multiupload_captions'  => 'Photo caption',
                    'multiupload_count'     => 3,
                    'skin'                  => 'default',
                )
            );
        }
    }
}