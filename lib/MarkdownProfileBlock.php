<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class MarkdownProfileBlock extends AccountProfileBlock
{
    protected $profile = null;

    function __construct(Action $out, Profile $profile)
    {
        parent::__construct($out, $profile);

        $this->profile = $profile;
    }

    function show() {
	    ProfileBlock::show();
    }

    function showDescription()
    {
        $description = $this->description();

        $markdown_parser = new \Michelf\Markdown();
        $rendered = $markdown_parser->defaultTransform($description);
        $safe = common_purify($rendered);

        if (!empty($description)) {
            $this->out->elementStart(
                'div',
                'profile_block_description'
            );
            $this->out->raw($safe);
            $this->out->elementEnd('div');
        }
    }
}

