<?php

namespace Give\Email;

use Give_Admin_Settings;

/**
 * @since 2.17.1
 */
class GlobalSettingValidator
{
    /**
     * @since 2.17.1
     */
    public function __invoke()
    {
        // Bailout.
        if (
            ! Give_Admin_Settings::is_saving_settings() ||
            'emails' !== give_get_current_setting_tab() ||
            ! isset($_GET['section'])
        ) {
            return;
        }

        add_filter($this->getFilterHookName(), [$this, 'validateSetting']);
    }

    /**
     * @since 2.17.1
     */
    public function validateSetting($value)
    {
        // Same unique email address for email recipients.
        $recipientEmails = array_unique(array_filter($value));

        // Set default email recipient to admin email.
        return $recipientEmails ?: [get_bloginfo('admin_email')];
    }

    /**
     * @since 2.17.1
     * Note: Filter hook defined in Give_Admin_Settings::save_fields function::1163
     *
     * @return string
     */
    private function getFilterHookName()
    {
        $email_type = give_get_current_setting_section();
        $settingName = "{$email_type}_recipient";

        return "give_admin_settings_sanitize_option_$settingName";
    }
}
