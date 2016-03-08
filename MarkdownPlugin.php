<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class MarkdownPlugin extends Plugin
{
    const VERSION = '0.0.7';

    // From /lib/util.php::common_render_text
    // We don't want to call it directly since we don't want to
    // run common_linkify() on the text
    function render_text($text)
    {
        $text = common_remove_unicode_formatting($text);

        $text = preg_replace('/[\x{0}-\x{8}\x{b}-\x{c}\x{e}-\x{19}]/', '', $text);
        $text = common_replace_urls_callback($text, 'common_linkify');

        // Link #hashtags
        $rendered = preg_replace_callback('/(^|\&quot\;|\'|\(|\[|\{|\s+)#([\pL\pN_\-\.]{1,64})/u',
                                          function ($m) { return "{$m[1]}#".common_tag_link($m[2]); }, $text);

        return $rendered;
    }

    // See: https://github.com/chimo/gs-markdown/issues/10
    function fix_whitespace($rendered)
    {
        // Remove <p>s
        $rendered = str_replace('<p>', '', $rendered);

        // Replace </p>s with <br><br>
        $rendered = str_replace('</p>', '<br><br>', $rendered);

        // Remove trailing <br><br>
        return preg_replace('/<br><br>$/', '', $rendered);
    }


    /**
     * Replace double <br>s with a line-break
     *
     * The following input:
     *      Foo
     *
     *      * list
     *      * item
     *
     * Has `$notice->rendered` like this:
     *      Foo<br><br>
     *      * list<br>
     *      * item
     *
     * Turns into this after `common_strip_html()`:
     *      Foo
     *      * list
     *      * item
     *
     * So we lose the blank line required by markdown to parse
     * the input as a list.
     *
     * This method takes the original `$notice->rendered` and makes it like this:
     *      Foo
     *
     *      * list<br>
     *      * item<br>
     *
     * The remaining <br>s are taken care of by `common_strip_html()`
     */
	function br2nl ($string)
	{
        return preg_replace('/(\<br(\s*)?\/?\>){2}/i', PHP_EOL, $string);
	}

    function onStartNoticeSave($notice)
    {
        $text = common_strip_html($this->br2nl($notice->rendered), true, true);

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

            // handle Markdown link forms in order not to convert doubly.
            $rendered = preg_replace('/\[([^]]+)\]\((<a [^>]+>)([^<]+)<\/a>\)/u', '\2\1</a>', $rendered);

            // Convert Markdown to HTML
            // TODO: Abstract the parser so we can call the same method regardless of lib
            switch($this->parser) {
                case 'gfm':
                    // Composer
                    require __DIR__ . '/vendor/autoload.php';

                    $this->parser = new \cebe\markdown\GithubMarkdown();
                    $rendered = $this->parser->parse($rendered);
                    break;
                default:
                    $this->parser = new \Michelf\Markdown();
                    $rendered = $this->parser->defaultTransform($rendered);
            }

            $notice->rendered = common_purify($this->fix_whitespace($rendered));
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
