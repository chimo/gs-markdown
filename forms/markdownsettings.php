<?php
if (!defined('GNUSOCIAL')) {
    exit(1);
}

class MarkdownSettingsForm extends Form
{
    function __construct($out=null)
    {
        parent::__construct($out);
    }

    function id()
    {
        return 'markdown_settings';
    }

    function formClass()
    {
        return 'form_settings';
    }

    function action()
    {
        return common_local_url('markdownsettings');
    }

    function formData()
    {
        $user = common_current_user();
        $profile = $user->getProfile();

        $this->out->elementStart('fieldset');

        $this->out->elementStart('ul', 'form_data');

        // Get current user settings
        $user_settings = Profile_prefs::getData($profile, MarkdownPlugin::NAME_SPACE, 'enabled', false);

        // Enabled?
        $this->li();
        $this->out->checkbox(
            'enabled',  // id
            'Parse my notices as markdown',  // label
            $user_settings // checked
        );
        $this->unli();

        $this->elementEnd('ul');
        $this->elementEnd('fieldset');
    }

    function formActions()
    {
        $this->out->submit('markdown-settings-submit', _m('BUTTON', 'Save'), 'submit', 'submit');
    }
}

