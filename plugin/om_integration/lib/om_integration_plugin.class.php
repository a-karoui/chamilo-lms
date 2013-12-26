<?php

class om_integrationPlugin extends Plugin
{
    public $is_course_plugin = true;

    //When creating a new course this settings are added to the course
    public $course_settings = array(
                    array('name' => 'om_button_record_and_store', 'type' => 'checkbox')
    );

    static function create()
    {
        static $result = null;
        return $result ? $result : $result = new self();
    }

    protected function __construct()
    {
        parent::__construct('2.0', 'Francis Gonzales', array('tool_enable' => 'boolean', 'host' =>'text', 'user' => 'text', 'pass' => 'text'));
    }

    function install()
    {
        $table = Database::get_main_table('plugin_om_meeting');
        $sql = "CREATE TABLE IF NOT EXISTS $table (
                id INT unsigned NOT NULL auto_increment PRIMARY KEY,
                c_id INT unsigned NOT NULL DEFAULT 0,
                meeting_name VARCHAR(255) NOT NULL DEFAULT '',
                attendee_pw VARCHAR(255) NOT NULL DEFAULT '',
                moderator_pw VARCHAR(255) NOT NULL DEFAULT '',
                record INT NOT NULL DEFAULT 0,
                status INT NOT NULL DEFAULT 0,
                created_at VARCHAR(255) NOT NULL,
                closed_at VARCHAR(255) NOT NULL,
                calendar_id INT DEFAULT 0,
                welcome_msg VARCHAR(255) NOT NULL DEFAULT '')";
        Database::query($sql);

        //Installing course settings
        $this->install_course_fields_in_all_courses();
    }

    function uninstall()
    {
        $t_settings = Database::get_main_table(TABLE_MAIN_SETTINGS_CURRENT);
        $t_options = Database::get_main_table(TABLE_MAIN_SETTINGS_OPTIONS);
        $t_tool = Database::get_course_table(TABLE_TOOL_LIST);

        //New settings

        $sql = "DELETE FROM $t_settings WHERE variable = 'om_integration_tool_enable'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'om_integration_pass'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'om_integration_user'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'om_integration_host'";
        Database::query($sql);

        //Old settings deleting just in case
        $sql = "DELETE FROM $t_settings WHERE variable = 'om_plugin'";
        Database::query($sql);
        $sql = "DELETE FROM $t_options WHERE variable  = 'om_plugin'";
        Database::query($sql);
//        $sql = "DELETE FROM $t_settings WHERE variable = 'bbb_plugin_host'";
//        Database::query($sql);
//        $sql = "DELETE FROM $t_settings WHERE variable = 'bbb_plugin_salt'";
//        Database::query($sql);

        //hack to get rid of Database::query warning (please add c_id...)
        $sql = "DELETE FROM $t_tool WHERE name = 'videoconference' AND c_id = c_id";
        Database::query($sql);

        $sql = "DROP TABLE IF EXISTS plugin_om_meeting";
        Database::query($sql);

        //Deleting course settings
        $this->uninstall_course_fields_in_all_courses();
    }
}