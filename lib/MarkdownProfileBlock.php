<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class MarkdownProfileBlock extends ProfileBlock
{
    protected $profile;

    function __construct(Action $out, Profile $profile)
    {
        parent::__construct($out);

        $this->profile = $profile;
    }

    function name()
    {
        return $this->profile->getBestName();
    }

    function url()
    {
        return $this->profile->profileurl;
    }

    function location()
    {
        return $this->profile->location;
    }

    function homepage()
    {
        return $this->profile->homepage;
    }

    function description()
    {
        return $this->profile->bio;
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

