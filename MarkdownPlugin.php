<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class MarkdownPlugin extends Plugin
{
    const VERSION = '0.0.2';

    function onStartNoticeSave($notice)
    {
        // Only run this on local notices
        if ($notice->isLocal()) {
            $text = $notice->content;

            // From /lib/util.php::common_render_text
            // We don't want to call it directly since we don't want to
            // run common_linkify() on the text
            $text = common_remove_unicode_formatting($text);
            $text = preg_replace('/[\x{0}-\x{8}\x{b}-\x{c}\x{e}-\x{19}]/', '', $text);

            // Link #hashtags
            $rendered = preg_replace_callback('/(^|\&quot\;|\'|\(|\[|\{|\s+)#([\pL\pN_\-\.]{1,64})/u',
            function ($m) { return "{$m[1]}#".common_tag_link($m[2]); }, $text);

            // Link @mentions, !mentions, @#mentions
            $rendered = common_linkify_mentions($rendered, $notice);

            // Convert Markdown to HTML
            $notice->rendered = \Michelf\Markdown::defaultTransform($rendered);
        }

        return true;
    }

    function onPluginVersion(&$versions)
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
