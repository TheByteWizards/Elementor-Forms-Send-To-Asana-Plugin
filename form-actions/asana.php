<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor form asana action.
 *
 * Custom Elementor form action which will send the form to asana
 *
 * @since 1.0.0
 */
class Ping_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base
{

    /**
     * Get action name.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'TBW_Asana';
    }

    /**
     * Get action label.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('TBW_Asana', 'elementor-forms-asana-action');
    }

    /**
     * Run action.
     *
     * @since 1.0.0
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {

        $settings = $record->get('form_settings');

        if (empty($settings['tbw_asana_action_api_key'])) {
            return;
        }

        if (empty($settings['tbw_asana_action_workspace_id']) && empty($settings['tbw_asana_action_project_id'])) {
            return;
        }

        $description = "";
        $raw_fields = $record->get('fields');
        foreach ($raw_fields as $id => $field) {
            $description .= ($id ?? '') . ": " . ($field['value'] ?? '') . "\r\n";
        }

        $post_data = [];

        if ($settings['tbw_asana_action_workspace_id'] ?? '' !== '') {
            $post_data['workspace'] = $settings['tbw_asana_action_workspace_id'];
        }

        if ($settings['tbw_asana_action_project_id'] ?? '' !== '') {
            $post_data['projects'][] = $settings['tbw_asana_action_project_id'];
        }

        if ($settings['tbw_asana_action_assignee_id'] ?? '' !== '') {
            $post_data['assignee'] = $settings['tbw_asana_action_assignee_id'];
        }

        $post_data['name'] = $settings['tbw_asana_action_title'] ?? ' No Title';

        $post_data['notes'] = get_site_url() . "\r\n" . $description ?? '';
        $to_send = [];
        $to_send = ['data' => $post_data];

        wp_remote_post(
            'https://app.asana.com/api/1.0/tasks',
            array(
                'method' => 'POST',
                'timeout' => 45,
                'httpversion' => '1.0',
                'blocking' => false,
                'headers' => [
                    'accept' => 'application/json',
                    'content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $settings['tbw_asana_action_api_key'],
                ],
                'body' => json_encode($to_send)
            )
        );

    }

    /**
     * Register action controls.
     *
     * @since 1.0.0
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {

        $widget->start_controls_section(
            'tbw_asana_action_section',
            [
                'label' => esc_html__('TBW Asana', 'elementor-forms-asana-action'),
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            'tbw_asana_action_api_key',
            [
                'label' => esc_html__('Asana API Key', 'elementor-forms-asana-action'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('[Required] Enter your Personal access token from asana. You can learn about creating one <a href="https://developers.asana.com/docs/personal-access-token" target="_new">here</a>.', 'elementor-forms-asana-action'),
            ]
        );

        $widget->add_control(
            'tbw_asana_action_workspace_id',
            [
                'label' => esc_html__('Workspace ID', 'elementor-forms-asana-action'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('[Optional only if entering a project gid below] Enter your workspace gid. You can find it <a href="https://app.asana.com/api/1.0/workspaces" target="_new">here</a>, when logged into Asana. Enter only the number for the workspace you want to use', 'elementor-forms-asana-action'),
            ]
        );

        $widget->add_control(
            'tbw_asana_action_project_id',
            [
                'label' => esc_html__('Project ID', 'elementor-forms-asana-action'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('[Optional] Enter your a project gid. You can find it <a href="https://app.asana.com/api/1.0/projects" target="_new">here</a>, when logged into Asana. Enter only the number for the project you want to use', 'elementor-forms-asana-action'),
            ]
        );

        $widget->add_control(
            'tbw_asana_action_assignee_id',
            [
                'label' => esc_html__('Assignee ID', 'elementor-forms-asana-action'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('[Optional] Enter the gid of the user you wish to assign the task to. You can find it <a href="https://app.asana.com/api/1.0/users" target="_new">here</a>, when logged into Asana. Enter only the number for the user you want to use', 'elementor-forms-asana-action'),
            ]
        );

        $widget->add_control(
            'tbw_asana_action_title',
            [
                'label' => esc_html__('Title', 'elementor-forms-asana-action'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('[Optional] Enter the title you would like to use for the asana ticket', 'elementor-forms-asana-action'),
            ]
        );

        $widget->end_controls_section();

    }

    /**
     * On export.
     *
     * @since 1.0.0
     * @access public
     * @param array $element
     */
    public function on_export($element)
    {
    }

}
