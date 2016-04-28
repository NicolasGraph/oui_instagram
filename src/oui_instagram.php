<?php

$plugin['name'] = 'oui_instagram';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.6.5-alpha.b';
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
oui_instagram => Instagram gallery
oui_instagram_access_token => Access token
oui_instagram_username => Default username
oui_instagram_user_id => Default user id
oui_instagram_cache_time => Default cache time
oui_instagram_hash_key => Cache file hash key
#@language fr-fr
oui_instagram => Galerie Instagram
oui_instagram_access_token => Access token
oui_instagram_username => Nom d'utilisateur par défaut
oui_instagram_user_id => Identifiant utilisateur par défaut
oui_instagram_cache_time => Durée du cache par défaut
oui_instagram_hash_key => Clé de hachage du fichier cache
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
# get an Instagram access token - *Important:* by default Instagram provides you a _basic_ "login permission":https://www.instagram.com/developer/authorization/ with your access token. It should be enough to pull Instagram images associated with a user id, nevetheless, you will need a _public_content_ scope/permission to use a username. You can easily get an access token with this scope form "Pixel Union":http://instagram.pixelunion.net/;
# Click _Options_ or visit your *Admin>Preferences* tab to fill the plugin prefs.

h2(#prefs). Preferences / options

* Access token - _Default: unset_ - A valid Instagram access token. 
* Default username - _Default: unset_ - The username of the Instagram account used by default (not needed if the user id is provided).
* Default user id - _Default: unset_ - The user id of the Instagram account used by default; faster than username!
* Default cache time — _Default: 0_ - Duration of the cache in seconds.
* Cache file hash key - _Default: a random number_ - A number used to hash the 32-character reference assigned to your Instagram query and to generate a shorter key for your cache file (you shouldn't need to change that).

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
* @cache_time="…"@ — _Default: 0_ - Duration of the cache in seconds.
* @class="…"@ – _Default: oui_instagram_images_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.
* @limit="…"@ — _Default: 10_ - The number of images to display (Instagram have a max limit 20 or 33 images depending on your access token login permission).
* @link="…"@ — _Default: auto_ - To apply a link around each generated image to the standard_resolution image. Valid values are auto (linked to the Instagram page), 1 (linked to the image url), 0.
* @type="…"@ — _Default: thumbnail_ - The image type to display. Valid values are thumbnail, low_resolution, standard_resolution.
* @user_id="…"@ - _Default: unset_ - An Instagram user id to override the default user id provided in the plugin preferences; faster than username!
* @username="…"@ - _Default: unset_ - An Instagram username to override the the default username provided in the plugin preferences.
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

h2(#licence). Licence

This plugin is distributed under "GPLv2":http://www.gnu.org/licenses/gpl-2.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

if (class_exists('\Textpattern\Tag\Registry')) {
    // Register Textpattern tags for TXP 4.6+.
    Txp::get('\Textpattern\Tag\Registry')
        ->register('oui_instagram_images')
        ->register('oui_instagram_image')
        ->register('oui_instagram_image_info')
        ->register('oui_instagram_image_url')
        ->register('oui_instagram_image_date')
        ->register('oui_instagram_image_author');
}

if (txpinterface === 'admin') {
    add_privs('prefs.oui_instagram', '1');
    add_privs('plugin_prefs.oui_instagram', '1');
    register_callback('oui_instagram_welcome', 'plugin_lifecycle.oui_instagram');
    register_callback('oui_instagram_install', 'prefs', null, 1);
    register_callback('oui_instagram_options', 'plugin_prefs.oui_instagram', null, 1);
}

function oui_instagram_welcome($evt, $stp)
{
    switch ($stp) {
        case 'installed':
        case 'enabled':
            oui_instagram_install();
            break;
        case 'deleted':
            if (function_exists('remove_pref')) {
                // Txp 4.6
                remove_pref(null, 'oui_instagram');
            } else {
                safe_delete('txp_prefs', "event='oui_instagram'");
            }
            safe_delete('txp_lang', "name LIKE 'oui\_instagram%'");
            break;
    }
}

function oui_instagram_install() {
    if (get_pref('oui_instagram_access_token', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_instagram_access_token', '', 'oui_instagram', PREF_PLUGIN, 'text_input', 20);
        } else {
            set_pref('oui_instagram_access_token', '', 'oui_instagram', PREF_ADVANCED, 'text_input', 20);
        }
    }
    if (get_pref('oui_instagram_username', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_instagram_username', '', 'oui_instagram', PREF_PLUGIN, 'text_input', 20);
        } else {
            set_pref('oui_instagram_username', '', 'oui_instagram', PREF_ADVANCED, 'text_input', 20);
        }
    }
    if (get_pref('oui_instagram_user_id', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_instagram_user_id', '', 'oui_instagram', PREF_PLUGIN, 'text_input', 20);
        } else {
            set_pref('oui_instagram_user_id', '', 'oui_instagram', PREF_ADVANCED, 'text_input', 20);
        }
    }
    if (get_pref('oui_instagram_cache_time', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_instagram_cache_time', '0', 'oui_instagram', PREF_PLUGIN, 'text_input', 20);
        } else {
            set_pref('oui_instagram_cache_time', '0', 'oui_instagram', PREF_ADVANCED, 'text_input', 20);
        }
    }
    if (get_pref('oui_instagram_hash_key', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_instagram_hash_key', mt_rand(100000, 999999), 'oui_instagram', PREF_PLUGIN, 'text_input', 20);
        } else {
            set_pref('oui_instagram_hash_key', mt_rand(100000, 999999), 'oui_instagram', PREF_ADVANCED, 'text_input', 20);
        }
    }
}

function oui_instagram_options() {
    if (defined('PREF_PLUGIN')) {
        $link = '?event=prefs';
    } else {
        $link = '?event=prefs&step=advanced_prefs';
    }
    header('Location: ' . $link);
}

function oui_instagram_images($atts, $thing=null) {
    global $username, $thisshot;

    extract(lAtts(array(
        'username'   => get_pref('oui_instagram_username'),
        'user_id'    => get_pref('oui_instagram_user_id'),
        'limit'      => '10',
        'type'       => 'thumbnail',
        'link'       => 'auto',
        'cache_time' => get_pref('oui_instagram_cache_time'),
        'wraptag'    => 'ul',
        'class'      => 'oui_instagram_images',
        'break'      => 'li',
        'label'      => '',
        'labeltag'   => '',
    ),$atts));

    $access_token = get_pref('oui_instagram_access_token');
    $hash_key =  get_pref('oui_instagram_hash_key');

    if (!$access_token) {
        trigger_error("oui_instagram requires an Instagram access token as a plugin preference");
        return;
    }

    // Prepare cache variables
    $keybase = md5($username.$limit.$type.$thing);
    $hash = str_split($hash_key);
    $cachekey='';
    foreach ($hash as $hashskip) {
        $cachekey .= $keybase[$hashskip];
    }
    $cachedate = get_pref('cacheset');
    $cachefile = find_temp_dir().DS.'oui_instagram_data_'.$cachekey;
    $cacheexists = file_exists($cachefile) ? true : false;

    $needcache = (($cache_time > 0) && ((!$cacheexists) || (time() - $cachedate) > $cache_time)) ? true : false;
    $readcache = (($cache_time > 0) && ($cacheexists)) ? true : false;

    // Cache_time is not set, or a new cache file is needed; throw a new request
    if ($needcache || $cache_time == 0) {

        // Get the user id if not set
        if(!$user_id) {
            if($username) {
                // Search for the user id…
                $user_idquery = json_decode(file_get_contents('https://api.instagram.com/v1/users/search?q='.$username.'&access_token='.$access_token));
                // …and check the result
                if(isset($user_idquery->data[0]->id)){
                    $user_id=$user_idquery->data[0]->id;
                } else {
                  trigger_error("oui_instagram was unable to find the user id of the following instagram username: ".$username);
                  return;
                }
            } else {
              trigger_error("oui_instagram_images tag requires a username or a user_id attribute.");
              return;
            }
        }

        // Get the Instagram feed…
        $pages_count = ceil(($limit / 20) -1);
        $last_limit = $limit % 20;
        $shots = array();

        for ($page = 0; $page <= $pages_count; $page++) {

            if ($page == 0 && $page != $pages_count) {

                $shots[] = json_decode(file_get_contents('https://api.instagram.com/v1/users/'.(int)$user_id.'/media/recent?access_token='.$access_token.'&count=20'));
                $next_shots = $shots[$page]->{'pagination'}->{'next_max_id'};

            } else if ($page == $pages_count) {

                $shots[] = json_decode(file_get_contents('https://api.instagram.com/v1/users/'.(int)$user_id.'/media/recent?access_token='.$access_token.'&count='.$last_limit.(($limit > 20) ? '&max_id='.$next_shots : '')));
                $next_shots = $shots[$page]->{'pagination'}->{'next_max_id'};

            } else {

                $shots[] = json_decode(file_get_contents('https://api.instagram.com/v1/users/'.(int)$user_id.'/media/recent?access_token='.$access_token.'&count=20'.'&max_id='.$next_shots));
                $next_shots = $shots[$page]->{'pagination'}->{'next_max_id'};
            }
        }


        // …and check the result
        for ($page = 0; $page <= $pages_count; $page++) {

            if(isset($shots[$page]->data)){

                foreach($shots[$page]->data as $thisshot) {

                    // single tag use
                    if ($thing === null) {

                        $url = $thisshot->{'images'}->{$type}->{'url'};
                        $width = $thisshot->{'images'}->{$type}->{'width'};
                        $height = $thisshot->{'images'}->{$type}->{'height'};
                        $caption = isset($thisshot->{'caption'}->{'text'}) ? $thisshot->{'caption'}->{'text'} : '';
                        $to = ($link == 'auto') ? $thisshot->{'link'} : $thisshot->{'images'}->{$type}->{'url'};

                        $data[] = ($link) ? href('<img src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" />',$to, ' title="'.$caption.'"') : '<img src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" />';
                        $out = (($label) ? doLabel($label, $labeltag) : '').\n
                               .doWrap($data, $wraptag, $break, $class);

                    // Conatiner tag use
                    } else {
                        $data[] = parse($thing);
                        $out = (($label) ? doLabel($label, $labeltag) : '').\n
                               .doWrap($data, $wraptag, $break, $class);
                    }
                }
            } else {
                trigger_error("oui_instagram was unable to get any content for the following user id: ".$user_id);
                return;
            }
        }
    }
    // Cache file is needed
    if ($needcache) {
        // Remove old cache files
        $oldcaches = glob($cachefile);
        if (!empty($oldcaches)) {
            foreach($oldcaches as $todel) {
                unlink($todel);
            }
        }
        // Time stamp and write the new cache files and return
        set_pref('cacheset', time(), 'oui_instagram', PREF_HIDDEN, 'text_input');
        $cache = fopen($cachefile,'w+');
        fwrite($cache,$out);
        fclose($cache);
    }

    // Cache is on and file is found, get it!
    if ($readcache) {
        $cache_out = file_get_contents($cachefile);
        return $cache_out;
    // No cache file :(
    } else {
        return $out;
    }
}

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