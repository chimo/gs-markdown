<?php
if (!defined('GNUSOCIAL')) {
    exit(1);
}

class MarkdownSettingsAction extends SettingsAction
{
    protected function doPost()
    {
        $currentUser = common_current_user();
        $isEnabled = $this->boolean('enabled', false);

        // Let it throw UserNoProfileException if it wants to;
        // there's no way for us to handle this anyway.
        $currentProfile = $currentProfile = $currentUser->getProfile();

        Profile_prefs::setData($currentProfile, MarkdownPlugin::NAME_SPACE, 'enabled', $isEnabled);

        return _('Settings saved.');
    }

    function title()
    {
        return _m('Markdown Settings');
    }

    function showContent()
    {
        $form = new MarkdownSettingsForm($this);
        $form->show();
    }
}


