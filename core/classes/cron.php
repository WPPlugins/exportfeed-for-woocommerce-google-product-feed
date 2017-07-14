<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
//Create a custom refresh_interval so that scheduled events will be able to display
//  in Cron job manager
function gcpf_add_xml_refresh_interval()
{
    $current_delay = get_option('gcp_feed_delay');

    return array(
        'refresh_interval' => array('interval' => $current_delay, 'display' => 'Google XML refresh interval'),
    );

}

if (!class_exists('GCPF_PCPCron')) {
    class GCPF_PCPCron
    {

        public static function doSetup()
        {
            add_filter('cron_schedules', 'gcpf_add_xml_refresh_interval');
            //Delete old (faulty) scheduled cron job from prior versions
            $next_refresh = wp_next_scheduled('gcpf_xml_updatefeeds_hook');
            if ($next_refresh)
                wp_unschedule_event($next_refresh, 'gcpf_xml_updatefeeds_hook');
        }

        public static function scheduleUpdate()
        {
            //Set the Cron job here. Params are (when, display, hook)
            $next_refresh = wp_next_scheduled('gcpf_update_cartfeeds_hook');
            if (!$next_refresh)
                wp_schedule_event(time(), 'refresh_interval', 'gcpf_update_cartfeeds_hook');
        }

    }
}
