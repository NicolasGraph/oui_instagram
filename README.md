# oui_instagram

Easily display Instagram recent images galleries.

## Plugin requirements

* required: [Textpattern CMS](http://textpattern.com/) 4.6+.
* recommended: [aks_cache](http://forum.textpattern.com/viewtopic.php?id=33460) or any partial caching system.

## Installation

1. [Download](https://github.com/NicolasGraph/oui_instagram/releases) the compiled plugin file.
1. Paste the content of the plugin file under the *Admin > Plugins* and click the _Upload_ button.
1. Confirm the plugin install by clicking the _Install_ button on the plugin preview page.
1. Enable the plugin and click _Options_ or visit your *Admin>Preferences* tab to fill the plugin prefs.

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
