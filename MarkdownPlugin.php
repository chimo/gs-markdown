<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class MarkdownPlugin extends Plugin
{
    const VERSION = '0.0.4';

    // From /lib/util.php::common_render_text
    // We don't want to call it directly since we don't want to
    // run common_linkify() on the text
    function render_text($text)
    {
        $text = common_remove_unicode_formatting($text);

        $text = preg_replace('/[\x{0}-\x{8}\x{b}-\x{c}\x{e}-\x{19}]/', '', $text);

        // Link #hashtags
        $rendered = preg_replace_callback('/(^|\&quot\;|\'|\(|\[|\{|\s+)#([\pL\pN_\-\.]{1,64})/u',
            function ($m) { return "{$m[1]}#".common_tag_link($m[2]); }, $text);

        return $rendered;
    }

    function onStartNoticeSave($notice)
    {
        $text = common_strip_html($notice->rendered, true, true);

        // Only run this on local notices
        if ($notice->isLocal()) {

            $rendered = $this->render_text($text);

            // Some types of notices do not have the hasParent() method, but they're not notices we are interested in
            if (method_exists($notice, 'hasParent')) {
                // Link @mentions, !mentions, @#mentions
                $rendered = common_linkify_mentions($rendered, $notice->getProfile(), $notice->hasParent() ? $notice->getParent() : null);
            }

            // Prevent leading #hashtags from becoming headers by adding a backslash
            // before the "#", telling markdown to leave it alone
            $repl_rendered = preg_replace('/^#<span class="tag">/u', '\\\\\\0', $rendered);

            // Only use the replaced value from above if it returned a success
            if ($rendered !== null) {
                $rendered = $repl_rendered;
            }

            // Convert Markdown to HTML
            $notice->rendered = \Michelf\Markdown::defaultTransform($rendered);
        }

        return true;
    }

    function onEndShowStyles($action)
    {
        $action->cssLink($this->path('css/markdown.css'));
    }

    function onPluginVersion(array &$versions)
    {
        $versions[] = array('name' => 'Markdown',
                            'version' => self::VERSION,
                            'author' => 'chimo',
                            'homepage' => 'https://github.com/chimo/gs-markdown',
                            'description' =>
                            // TRANS: Plugin description.
                            _m('Use markdown syntax'));
        return true;
    }
}
