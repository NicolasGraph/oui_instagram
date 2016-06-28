<?php

$plugin['name'] = 'oui_instagram';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.6.9-dev';
$plugin['author'] = 'Nicolas Morand';
$plugin['author_uri'] = 'https://github.com/NicolasGraph';
$plugin['description'] = 'Recent Instagram images gallery';

$plugin['order'] = 5;

$plugin['type'] = 1;

// Plugin 'flags' signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use.
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

// $plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;
$plugin['flags'] = 3;

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
$plugin['textpack'] = <<< EOT
#@public
#@language en-gb
oui_instagram => Instagram gallery (oui_instagram)
oui_instagram_access_token => Access token
oui_instagram_cache_time => Default cache time
oui_instagram_user_id_placeholder => Filled after saving if a username is provided.
oui_instagram_username_placeholder => optional
#@language fr-fr
oui_instagram => Galerie Instagram (oui_instagram)
oui_instagram_access_token => Access token
oui_instagram_cache_time => Durée du cache par défaut en minutes
oui_instagram_user_id_placeholder => Renseigné après sauvegarde si un nom d'utilisateur est fournit.
oui_instagram_username_placeholder => Optionnel
EOT;

if (!defined('txpinterface'))
    @include_once('zem_tpl.php');

if (0) {

?>
# --- BEGIN PLUGIN HELP ---
h1. oui_instagram

Easily display recent images from an Instagram account.

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
* "Preferences":#prefs
* "Tags":#tags
** "oui_instagram_images":#oui_instagram_images
** "oui_instagram_image":#oui_instagram_image
** "oui_instagram_image_info":#oui_instagram_image_info
** "oui_instagram_image_url":#oui_instagram_image_url
** "oui_instagram_image_date":#oui_instagram_image_date
** "oui_instagram_image_author":#oui_instagram_image_author
* "Examples":#examples
** "Single tag":#single_tag
** "Container tag":#container_tag
* "Author":#author
* "Licence":#licence

h2(#requirements). Plugin requirements

oui_instagram’s minimum requirements:

* Textpattern 4.5+

h2(#installation). Installation

# Paste the content of the plugin file under the *Admin > Plugins*, upload it and install;
# Click _Options_ or visit your *Admin>Preferences* tab to fill the plugin prefs.

h2(#prefs). Preferences / options

* *Access token* - _Default: unset_ - A valid Instagram access token.
*Important:* you can easily get an access token from Instagram or by using a webservice like "Pixel Union":http://instagram.pixelunion.net/. However by default, this access token will allow you to display images from your own account only. If you need to use other accounts, you will need to create an Instagram client and invite users in the Sandbox, or ask a special permission and register your app. See the "Instagram developer documentation":https://www.instagram.com/developer/ for more informations.
* *Default cache time* — _Default: 0_ - Duration of the cache in minutes.

h2(#tags). Tags

h3(#oui_instagram_images). oui_instagram_images

Displays a recent images list.
Can be used as a single or a container tag.

bc. <txp:oui_instagram_images />

or

bc. <txp:oui_instagram_images>
[…]
</txp:oui_instagram_images>

h4. Attributes

_(Alphabetical order)_

* @break="…"@ - _Default: li_ - The HTML tag used around each generated image.
* @cache_time="…"@ — _Default: 5 (set via the preferences)_ - Duration of the cache in minutes.
* @class="…"@ – _Default: oui_instagram_images_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.
* @limit="…"@ — _Default: 10_ - The number of images to display. If the @limit@ value is greater than _20_, several requests will be thrown to pull your images.
* @link="…"@ — _Default: auto_ - To apply a link around each generated image to the standard_resolution image. Valid values are auto (linked to the Instagram page), 1 (linked to the image url), 0.
* @type="…"@ — _Default: thumbnail_ - The image type to display. Valid values are thumbnail, low_resolution, standard_resolution.
* @user_id="…"@ - _Default: unset_ - Provide an Instagram username to generate a gallery from different account than the one associated with the access token plugin preference; it is faster than username.
* @username="…"@ - _Default: unset_ - Provide an Instagram username to generate a gallery from different account than the one associated with the access token plugin preference.
* @wraptag="…"@ - _Default: ul_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image). oui_instagram_image

Displays each image in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image />

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: oui_instagram_image_ - The css class to apply to the @img@ HTML tag or to the HTML tag assigned to @wraptag@.
* @type="…"@ — _Default: thumbnail_ - The image type to display. Valid values are thumbnail, low_resolution, standard_resolution.
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image_info). oui_instagram_image_info

Displays each image info in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_info />

h4. Attributes

_(Alphabetical order)_

* @break="…"@ — _Default: unset_ - The HTML tag used around each generated info.
* @class="…"@ — _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @type="…"@ — _Default: caption_ - The information type to display. Valid values are caption, likes, comments.
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image_url). oui_instagram_image_url

Uses each image url/link in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_url />

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the @a@ HTML tag if link is defined or to the HTML tag assigned to @wraptag@.
* @link="…"@ — _Default: auto_ - To apply a link to the standard_resolution image. Valid values are auto (link container tag only), 1, 0.
* @type="…"@ — _Default: instagram_ - The url type to use. Valid values are thumbnail, low_resolution, standard_resolution, instagram.
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image_date). oui_instagram_image_date

Displays each image date in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_date />

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset - The css class to apply to the HTML tag assigned to @wraptag@.
* @format="…"@ — _Default: the Archive date format set in the preferences_ - To adjust the display of the date to taste. Valid values are any valid strftime() string values.
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image_author). oui_instagram_image_author

Displays each image author in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_author />

_(Alphabetical order)_

* @class="…"@ — _Default: unset - The css class to apply to the @a@ HTML tag assigned by @link="1"@ or to the HTML tag assigned to @wraptag@.
* @link="…"@ — _Default: 0_ - To apply a link around the generated content.
* @title="…"@ — _Default: 1_ - To show the full name (1) or the username (0).
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h2(#examples). Examples

h3(#single_tag). Example 1: single tag use

bc. <txp:oui_instagram_images />

h3(#container_tag). Example 2: container tag use

bc. <txp:oui_instagram_images username="cercle_magazine">
    <txp:oui_instagram_image_url><txp:oui_instagram_image /></txp:oui_instagram_image_url>
    <txp:oui_instagram_image_info />
    <txp:oui_instagram_image_date />, <txp:oui_instagram_image_date />
</txp:oui_instagram_images>

h2(#author). Author

"Nicolas Morand":https://github.com/NicolasGraph
_Thank you to the Textpattern community and the core team._

h2(#licence). Licence

This plugin is distributed under "GPLv2":http://www.gnu.org/licenses/gpl-2.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

/**
 * Register tags for Txp 4.6+.
 */
if (class_exists('\Textpattern\Tag\Registry')) {
    Txp::get('\Textpattern\Tag\Registry')
        ->register('oui_instagram_images')
        ->register('oui_instagram_image')
        ->register('oui_instagram_image_info')
        ->register('oui_instagram_image_url')
        ->register('oui_instagram_image_date')
        ->register('oui_instagram_image_author');
}

/**
 * Register callbacks.
 */
if (txpinterface === 'admin') {

    add_privs('prefs.oui_instagram', '1');
    add_privs('plugin_prefs.oui_instagram', '1');

    register_callback('oui_instagram_welcome', 'plugin_lifecycle.oui_instagram');
    register_callback('oui_instagram_install', 'prefs', null, 1);
    register_callback('oui_instagram_options', 'plugin_prefs.oui_instagram', null, 1);

    $prefList = oui_instagram_preflist();
    foreach ($prefList as $pref => $options) {
        register_callback('oui_instagram_pophelp', 'admin_help', $pref);
    }
}

/**
 * Get external popHelp contents
 */
function oui_instagram_pophelp($evt, $stp, $ui, $vars) {
    return str_replace(HELP_URL, 'http://help.ouisource.com/', $ui);
}

/**
 * Handler for plugin lifecycle events.
 *
 * @param string $evt Textpattern action event
 * @param string $stp Textpattern action step
 */
function oui_instagram_welcome($evt, $stp)
{
    switch ($stp) {
        case 'enabled':
            oui_instagram_install();
            break;
        case 'deleted':
            function_exists('remove_pref')
                ? remove_pref(null, 'oui_instagram')
                : safe_delete('txp_prefs', "event='oui_instagram'");
            safe_delete('txp_lang', "name LIKE 'oui\_instagram%'");
            break;
    }
}

/**
 * Jump to the prefs panel.
 */
function oui_instagram_options() {
    $link = defined('PREF_PLUGIN') ? '?event=prefs' : '?event=prefs#prefs_group_oui_instagram';
    header('Location: ' . $link);
}

/**
 * Set prefs through:
 *
 * PREF_PLUGIN for 4.5
 * PREF_ADVANCED for 4.6+
 */
function oui_instagram_preflist() {
    $prefList = array(
        'oui_instagram_access_token' => array(
            'value'      => '',
            'event'      => 'oui_instagram',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
            'widget'     => 'text_input',
            'position'   => '10',
            'is_private' => false,
        ),
        'oui_instagram_cache_time' => array(
            'value'      => '5',
            'event'      => 'oui_instagram',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
            'widget'     => 'text_input',
            'position'   => '40',
            'is_private' => false,
        ),
        'oui_instagram_hash_key' => array(
            'value'      => mt_rand(100000, 999999),
            'event'      => 'oui_instagram',
            'visibility' => PREF_HIDDEN,
            'widget'     => 'text_input',
            'position'   => '50',
            'is_private' => false,
        ),
        'oui_instagram_cache_set' => array(
            'value'      => time(),
            'event'      => 'oui_instagram',
            'visibility' => PREF_HIDDEN,
            'widget'     => 'text_input',
            'position'   => '60',
            'is_private' => false,
        ),
    );
    return $prefList;
}


function oui_instagram_install() {

    $prefList = oui_instagram_preflist();

    foreach ($prefList as $pref => $options) {
        if (get_pref($pref, null) === null) {
            set_pref(
                $pref,
                $options['value'],
                $options['event'],
                $options['visibility'],
                $options['widget'],
                $options['position'],
                $options['is_private']
            );
        }
    }
}

/**
 * Required field for preferences
 */
function oui_instagram_required_input($name, $val) {
    return fInput('text', $name, $val, '', '', '', $size = 32, '', $name, '', $required = true);
}

/**
 * Add a placeholder to the username field.
 */
function oui_instagram_username_input($name, $val) {
    return fInput('text', $name, $val, '', '', '', $size = 32, '', $name, '', '', $placeholder = gTxt('oui_instagram_username_placeholder'));
}

/**
 * Disable the user id preference field
 * as it is now automatically filled on prefs saving.
 */
function oui_instagram_user_id_input($name, $val) {
    return fInput('text', $name, $val, '', '', '', $size = 32, '', $name, '$disabled = true', '', $placeholder = gTxt('oui_instagram_user_id_placeholder'));
}

/**
 * Main plugin function.
 *
 * Pull the images if needed;
 * parse and cache the gallery;
 * display the content.
 */
function oui_instagram_images($atts, $thing=null) {
    global $thisshot;

    extract(lAtts(array(
        'username'   => '',
        'user_id'    => '',
        'limit'      => '10',
        'type'       => 'thumbnail',
        'link'       => 'auto',
        'cache_time' => '',
        'wraptag'    => 'ul',
        'class'      => 'oui_instagram_images',
        'break'      => 'li',
        'label'      => '',
        'labeltag'   => '',
    ),$atts));

    $access_token = get_pref('oui_instagram_access_token');

    if (!$access_token) {
        trigger_error("oui_instagram requires an Instagram access token as a plugin preference");
        return;
    }

    if (!$user_id && !$username) {
        $user_id = 'self';
    }

    if (!$cache_time) {
        $cache_time = get_pref('oui_instagram_cache_time');
    }

    // Prepare the cache file name.
    $keybase = md5($username.$limit.$type.$thing);
    $hash = str_split(get_pref('oui_instagram_hash_key'));
    $cachekey='';
    foreach ($hash as $hashskip) {
        $cachekey .= $keybase[$hashskip];
    }
    $cachefile = find_temp_dir().DS.'oui_instagram_data_'.$cachekey;

    // Main cache conditioning variable.
    $needquery = (!file_exists($cachefile) || (time() - get_pref('oui_instagram_cache_set')) > ($cache_time *  60)) ? true : false;

    // New query needed.
    if ($needquery) {

        // Get the user id if not set.
        if($username) {
            // Search for the user id…
            $user_idquery = json_decode(file_get_contents('https://api.instagram.com/v1/users/search?q='.$username.'&access_token='.$access_token));
            // …and check the result.
            foreach($user_idquery->data as $user) {
                if($user->username == $username) {
                    $user_id = $user->id;
                }
            }
        }

        // Get the Instagram feed per 20 images because of the Instagram limit…
        $pages_count = ceil($limit / 20);
        $shots = array();

        for ($page = 1; $page <= $pages_count; $page++) {

            $shots[$page] = json_decode(file_get_contents('https://api.instagram.com/v1/users/'.$user_id.'/media/recent?access_token='.$access_token.'&count='.(($page == $pages_count) ? ($limit % 20) : '20').(($page == 1) ? '' : $next_shots)));

            ($page != $pages_count) ? $next_shots = '&max_id='.$shots[$page]->{'pagination'}->{'next_max_id'} : '';

            // …and check the result.
            if(isset($shots[$page]->data)){

                foreach($shots[$page]->data as $thisshot) {

                    // single tag use.
                    if ($thing === null) {

                        $url = $thisshot->{'images'}->{$type}->{'url'};
                        $width = $thisshot->{'images'}->{$type}->{'width'};
                        $height = $thisshot->{'images'}->{$type}->{'height'};
                        $caption = isset($thisshot->{'caption'}->{'text'}) ? $thisshot->{'caption'}->{'text'} : '';
                        $to = ($link == 'auto') ? $thisshot->{'link'} : $thisshot->{'images'}->{$type}->{'url'};

                        $data[] = ($link) ? href('<img src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" />',$to, ' title="'.$caption.'"') : '<img src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" />';
                        $out = (($label) ? doLabel($label, $labeltag) : '').\n
                               .doWrap($data, $wraptag, $break, $class);

                    // Conatiner tag use.
                    } else {

                        $data[] = parse($thing);
                        $out = (($label) ? doLabel($label, $labeltag) : '').\n
                               .doWrap($data, $wraptag, $break, $class);
                    }
                }
            } else {
                trigger_error("Something went wrong while oui_instagram tried to get your feed");
                return;
            }
        }
        update_lastmod();

        // Cache file needed.
        if ($cache_time > 0) {
            // Remove old cache files.
            $oldcaches = glob($cachefile);
            if (!empty($oldcaches)) {
                foreach($oldcaches as $todel) {
                    unlink($todel);
                }
            }
            // Time stamp and write the new cache files and return.
            set_pref('oui_instagram_cache_set', time());
            $cache = fopen($cachefile,'w+');
            fwrite($cache,$out);
            fclose($cache);
        }
    }

    // Return the cache content or the generated images.
    if (!$needquery && $cache_time > 0) {
        $cache_out = file_get_contents($cachefile);
        return $cache_out;
    } else {
        return $out;
    }
}

/**
 * Display each image in a oui_instagram_images context.
 */
function oui_instagram_image($atts) {
    global $thisshot;

    extract(lAtts(array(
        'type'    => 'thumbnail',
        'class'   => '',
        'wraptag' => '',
    ),$atts));

    $url = $thisshot->{'images'}->{$type}->{'url'};
    $width = $thisshot->{'images'}->{$type}->{'width'};
    $height = $thisshot->{'images'}->{$type}->{'height'};
    $caption = isset($thisshot->{'caption'}->{'text'}) ? $caption = $thisshot->{'caption'}->{'text'} : '';

    $out = '<img src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" ';
    $out .= ($wraptag) ? '' : ($class) ? 'class="'.$class.'" />' : '/>';

    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
}

/**
 * Display each image url in a oui_instagram_images context.
 */
function oui_instagram_image_url($atts, $thing=null) {
    global $thisshot;

    extract(lAtts(array(
        'type'    => 'instagram',
        'wraptag' => '',
        'class'   => '',
        'link'    => 'auto',
    ),$atts));

    $validTypes = array('instagram', 'thumbnail', 'low_resolution', 'standard_resolution');

    if (in_array($type, $validTypes)) {
        $url = ($type == 'instagram') ? $thisshot->{'link'} : $thisshot->{'images'}->{$type}->{'url'};
    } else {
        trigger_error("unknown attribute value; oui_instagram_image_url type attribute accepts the following values: instagram, thumbnail, low_resolution, standard_resolution");
        return;
    }

    $validLinks = array('auto', '1', '0');

    if (in_array($link, $validLinks)) {
        $link = ($link == 'auto') ? (($thing) ? 1 : 0) : $link;
        $out = ($thing) ? parse($thing) : $url;
        $out = ($link) ? href($out, $url, ($wraptag) ? '' : ' class="'.$class.'"') : $out;
        return doTag($out, $wraptag, $class);
    } else {
        trigger_error("unknown attribute value; oui_instagram_image_url link attribute accepts the following values: auto, 1, 0");
        return;
    }
}

/**
 * Display each image information in a oui_instagram_images context.
 */
function oui_instagram_image_info($atts) {
    global $thisshot;

    extract(lAtts(array(
        'wraptag' => '',
        'class'   => '',
        'break'   => '',
        'type'    => 'caption',
    ),$atts));

    $validTypes = array('caption', 'likes', 'comments');
    $types = do_list($type);

    $out = array();
    foreach ($types as $type) {
        $data = ($type=='caption') ? 'text' : 'count';
        if (in_array($type, $validTypes)) {
            $out[] = isset($thisshot->{$type}->{$data}) ? $thisshot->{$type}->{$data} : '';
        }
    }

    return doWrap($out, $wraptag, $break, $class);
}

/**
 * Display each image date in a oui_instagram_images context.
 */
function oui_instagram_image_date($atts) {
    global $thisshot;

    extract(lAtts(array(
        'wraptag' => '',
        'class'   => '',
        'format'  => '',
    ),$atts));

    $date = $thisshot->{'caption'}->{'created_time'};

    $out = fileDownloadFormatTime(array(
        'ftime'  => $date,
        'format' => $format,
    ));

    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
}

/**
 * Display each image author in a oui_instagram_images context.
 */
function oui_instagram_image_author($atts) {
    global $thisshot;

    extract(lAtts(array(
        'wraptag' => '',
        'class'   => '',
        'link'    => 0,
        'title'   => 1,
    ), $atts));

    $author = ($title) ? $thisshot->{'user'}->{'username'} : $thisshot->{'user'}->{'full_name'};
    $out = ($link) ? href($author, 'http://instagram.com/'.$username, ($wraptag) ? '' : ' class="'.$class.'"') : $author;

    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
}

# --- END PLUGIN CODE ---

?>
