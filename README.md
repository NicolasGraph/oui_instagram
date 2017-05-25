# oui_instagram

Easily display Instagram recent images galleries.

## Plugin requirements

* required: [Textpattern CMS](http://textpattern.com/) 4.6+.
* recommended: [aks_cache](http://forum.textpattern.com/viewtopic.php?id=33460) or any partial caching system.

## Installation

### From the admin interface

1. [Download](https://github.com/NicolasGraph/oui_instagram/releases) the compiled plugin file or the source to compile a customized file.
2. Paste the content of the compiled plugin file under the "Admin > Plugins":?event=plugin tab and click the _Upload_ button.
3. Confirm the plugin install by clicking the _Install_ button on the plugin preview page.
4. Enable the plugin and click _Options_ or visit your *Admin > Preferences* tab to fill the plugin prefs.

### Via Composer

After [installing Composer](https://getcomposer.org/doc/00-intro.md)…

1. Target your project directory:
`$ cd /path/to/your/textpattern/installation/dir`
2. If it's not already done, lock your version of Txp:
`$ composer require textpattern/lock:4.6.2`, where `4.6.2` is the Txp version in use.
3. Install oui_instagram:
`$ composer require oui/oui_instagram`
4. Connect to the Txp admin interface and click _Options_ or visit your *Admin > Preferences* tab to fill the plugin prefs.

## Documentation

Check the embedded Help file for more informations.

## Examples

### Single tag use

```xml
// Instagram username as a link to the related account.
<txp:oui_insta_user />

// List of recent Instagram images linked to their related Instagram pages.
<txp:oui_insta_images />
```

### Container tag use

```xml
// Instagram avatar as a link to the related account.
<txp:oui_insta_user>
    <txp:oui_insta_user_info type="avatar" link="Instagram" />
<txp:oui_insta_user>

// List of recent Instagram images linked to their related Instagram pages.
// Each image is folowed by its caption, its author and its post date.
<txp:oui_insta_images>
    <txp:oui_insta_image_url><txp:oui_insta_image /></txp:oui_insta_image_url>
    <txp:oui_insta_image_info />
    <txp:oui_insta_image_author />, <txp:oui_insta_image_date />
</txp:oui_insta_images>
```

## Author

[Nicolas Morand](https://twitter.com/NicolasGraph)
*Thank you to the Textpattern community and the core team.*

## License

This plugin is distributed under [GPLv2](http://www.gnu.org/licenses/gpl-2.0.fr.html).
