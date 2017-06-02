# Use Markdown in your GNU social notices

## Installation

1. Navigate to your `/local/plugins` directory (create it if it doesn't exist)
1. `git clone https://github.com/chimo/gs-markdown.git Markdown`

### Activate the feature for your user account

Because the markdown plugin changes the way notices are rendered significantly,
it is disabled by default.

Users can activate the plugin for their account in the "Settings > Markdown" section.

### Built-in markdown parser

Make sure the files are in a folder called `Markdown` if they're not already  
Put the folder in your `/plugins/` directory  
Tell `/config.php` to use it with:

```
    addPlugin('Markdown');
```

You can now use markdown syntax in your notices.

### Github Flavored Markdown

If you want to use [Github Flavored Markdown](https://help.github.com/articles/github-flavored-markdown/)
instead of the built-in markdown library, run `composer install` in the plugin directory (assuming you have
[composer](https://getcomposer.org/) installed).

And tell the plugin to use GFM by using the following in your `/config.php`:

```
    addPlugin('Markdown', array('parser' => 'gfm'));
```

You can now use GFM syntax in your notices.

