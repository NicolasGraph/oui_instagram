<?php

$plugin['name'] = 'oui_instagram';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.5.2bêta';
$plugin['author'] = 'Nicolas Morand';
$plugin['author_uri'] = 'https://github.com/NicolasGraph';
$plugin['description'] = 'Instagram gallery';

$plugin['order'] = 5;

$plugin['type'] = 1;

// Plugin 'flags' signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use.
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

// $plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;
$plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;

if (!defined('txpinterface'))
    @include_once('zem_tpl.php');

if (0) {

?>
# --- BEGIN PLUGIN HELP ---
h1. oui_instagram (Bêta)

Easily display recent images from an Instagram account.

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
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

# Paste the content of the plugin file under the *Admin > Plugins*, upload it and install.

h2(#tags). Tags

h3(#oui_instagram_images). oui_instagram_images

Displays a recent images list.

bc. <txp:oui_instagram_images />

h4. Attributes 

h5. Required

* @username="…"@ - _Default: unset_ - The username of the Instagram account.

h5. Recommended

* @cache_time="…"@ — _Default: 0_ - Duration of the cache in seconds.

h5. Optional

* @break="…"@ - _Default: li_ - The HTML tag used around each generated image.
* @class="…"@ – _Default: oui_instagram_images_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.
* @limit="…"@ — _Default: 10_ - The number of images to display.
* @link="…"@ — _Default: auto_ - To apply a link around each generated image to the standard_resolution image. Valid values are auto (linked to the Instagram page), 1 (linked to the image url), 0.
* @type="…"@ — _Default: thumbnail_ - The image type to display. Valid values are thumbnail, low_resolution, standard_resolution.
* @wraptag="…"@ - _Default: ul_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image). oui_instagram_image

Displays each image in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image />

h4. Attributes

* @class="…"@ — _Default: oui_instagram_image_ - The css class to apply to the @img@ HTML tag.
* @type="…"@ — _Default: thumbnail_ - The image type to display. Valid values are thumbnail, low_resolution, standard_resolution.

h3(#oui_instagram_image_info). oui_instagram_image_info

Displays each image info in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_info />

h4. Attributes

* @break="…"@ — _Default: unset_ - The HTML tag used around each generated info. 
* @class="…"@ — _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@. 
* @type="…"@ — _Default: caption_ - The information type to display. Valid values are caption, likes, comments.
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image_url). oui_instagram_image_url

Uses each image url/link in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_url />

h4. Attributes

* @class="…"@ — _Default: unset_ - The css class to apply to the @a@ HTML tag if link is defined.
* @link="…"@ — _Default: auto_ - To apply a link to the standard_resolution image. Valid values are auto (link container tag only), 1, 0.
* @type="…"@ — _Default: instagram_ - The url type to use. Valid values are thumbnail, low_resolution, standard_resolution, instagram.

h3(#oui_instagram_image_date). oui_instagram_image_date

Displays each image date in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_date />

h4. Attributes

* @class="…"@ — _Default: unset - The css class to apply to the HTML tag assigned to @wraptag@.
* @format="…"@ — _Default: the Archive date format set in the preferences_ - To adjust the display of the date to taste. Valid values are any valid strftime() string values.
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h3(#oui_instagram_image_author). oui_instagram_image_author

Displays each image author in a @oui_instagram_images@ container tag.

bc. <txp:oui_instagram_image_author />

* @class="…"@ — _Default: unset - The css class to apply to the HTML tag assigned to @wraptag@.
* @link="…"@ — _Default: 0_ - To apply a link around the generated content.  
* @title="…"@ — _Default: 1_ - To show the full name (1) or the username (0). 
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h2(#examples). Examples

h3(#single_tag). Example 1: single tag use

bc. <txp:oui_instagram_images username="cercle_magazine" />

h3(#container_tag). Example 2: container tag use

bc. <txp:oui_instagram_images username="cercle_magazine">
    <txp:oui_instagram_image_url><txp:oui_instagram_image /></txp:oui_instagram_image_url>
    <txp:oui_instagram_image_info />
    <txp:oui_instagram_image_date />, <txp:oui_instagram_image_date />
</txp:oui_instagram_images>

h2(#author). Author

"Nicolas Morand":https://github.com/NicolasGraph, from a "NOE interactive tip":http://noe-interactive.com/comment-integrer-ses-photos-instagram-sur-son-site.

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
        ->register('oui_instagram_images_date')
        ->register('oui_instagram_images_author');
}

//From instagramPhp by NOE interactive
class instagramPhp{
    /*
     * Attributes
     */
    private $username, //Instagram username
            $access_token, //Your access token
            $userid; //Instagram userid
    
    /*
     * Constructor
     */
    function __construct($username='',$access_token='',$cache_time='') {
        if(empty($username) || empty($access_token)){
            trigger_error("empty username or access token.");
            return;
        } else {
            $this->username=$username;
            $this->access_token = $access_token;
            $this->cache_time = $cache_time;
        }
    }
    /*
     * The api works mostly with user ids, but it's easier for users to use their username.
     * This function gets the userid corresponding to the username
     */
    public function getUserIDFromUserName(){
        if(strlen($this->username)>0 && strlen($this->access_token)>0){
            //Search for the username
            $useridquery = $this->queryInstagram('https://api.instagram.com/v1/users/search?q='.$this->username.'&access_token='.$this->access_token);
            if(!empty($useridquery) && $useridquery->meta->code=='200' && $useridquery->data[0]->id>0){
                //Found
                $this->userid=$useridquery->data[0]->id;
            } else {
                //Not found
              trigger_error("unknown attribute value; oui_instagram username attribute and/or access token preference is not valid.");
              return;            
            }
        } else {
          trigger_error("empty username or access token.");
          return; 
        }
    }
    /*
     * Get the most recent media published by a user.
     * you can use the $args array to pass the attributes that are used by the GET/users/user-id/media/recent method
     */
    public function getUserMedia($args=array()){
        if($this->userid<=0){
            //If no user id, get user id
            $this->getUserIDFromUserName();
        }
        if($this->userid>0 && strlen($this->access_token)>0){
            $qs='';
            if(!empty($args)){ $qs = '&'.http_build_query($args); } //Adds query string if any args are specified
            $images = $this->queryInstagram('https://api.instagram.com/v1/users/'.(int)$this->userid.'/media/recent?access_token='.$this->access_token.$qs); //Get images
            if($images->meta->code=='200'){
                return $images;
            } else {
                $this->error('getUserMedia');
            }
        } else {
          trigger_error("unknown attribute value; oui_instagram username attribute and/or access token preference is not valid.");
          return;
        }
    }
    /*
     * Common mechanism to query the instagram api
     */
    public function queryInstagram($url){
        //prepare caching
        $cachefolder = find_temp_dir().DS;
        $cachekey = md5($url);
        $cachedate = get_pref('cachedate');
        $cacheoutdate = (time() - $cachedate);
        $cachefile = $cachefolder.'oui_instagram_data_'.$cachekey.'.txt';
        //If not cached, -> instagram request
        if(!file_exists($cachefile) || $cacheoutdate > $this->cache_time){
            //Request
            $request='error';
            if(!extension_loaded('openssl')){ $request = 'This class requires the php extension open_ssl to work as the instagram api works with httpS.'; }
            else { $request = file_get_contents($url); }
            //remove old caches
            $oldcaches = glob($cachefolder.$cachekey."*.txt");
            if(!empty($oldcaches)) {
                foreach($oldcaches as $todel) {
                    unlink($todel);
                }
            }
            //Cache result
            set_pref('cachedate', time(), 'oui_instagram', PREF_HIDDEN, 'text_input'); 
            $rh = fopen($cachefile,'w+');
            fwrite($rh,$request);
            fclose($rh);
        }
        //Execute and return query
        $query = json_decode(file_get_contents($cachefile));
        return $query;
    }

}

function oui_instagram_images($atts, $thing=null) {
    global $username, $images, $image;
    
    extract(lAtts(array(
        'username'    => '',
        'limit'  => '10',
        'type'    => 'thumbnail',
        'link' => 'auto',
        'cache_time' => '0',
        'wraptag'     => 'ul',
        'class'       => 'oui_instagram_images',
        'break'       => 'li',
        'label'       => '',
        'labeltag'    => '',
    ),$atts));

    $access_token = '1517036843.ab103e5.2e484d7e57514253abb5d838d54511ca';

    if(!empty($username)){    
        $isg = new instagramPhp($username,$access_token,$cache_time); // instanciates the class with the parameters
        $images = $isg->getUserMedia(array('count'=>$limit)); // Get the images from instagram

        if(!empty($images->data)){

            foreach($images->data as $image){
                if ($thing===null) {
                    $url = $image->{'images'}->{$type}->{'url'};
                    $width = $image->{'images'}->{$type}->{'width'};
                    $height = $image->{'images'}->{$type}->{'height'};
                    $caption = $image->{'caption'}->{'text'};
                    $to = ($link == 'auto') ? $image->{'link'} : $image->{'images'}->{$type}->{'url'};

                    $out[]= ($link) ? href('<img class="'.$class.'" src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" />',$to, ' title="'.$caption.'"') : '<img class="'.$class.'" src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" />';
                    } else {
                    $out[]= parse($thing);
                    }
            }

            return doWrap($out, $wraptag, $break, $class, '');
            
        } else {
            trigger_error("nothing to display; oui_instagram is unable to find any data to display.");
            return;
        }
        trigger_error("Missing required attribute value; oui_instagram requires a username.");
        return;
    }

}

function oui_instagram_image($atts) {
    global $images, $image;

    extract(lAtts(array(
        'type'    => 'thumbnail',
        'class'    => '',
    ),$atts));
    
    $url = $image->{'images'}->{$type}->{'url'};
    $width = $image->{'images'}->{$type}->{'width'};
    $height = $image->{'images'}->{$type}->{'height'};
    $caption = $image->{'caption'}->{'text'};
    
    $out = '<img src="'.$url.'" alt="'.$caption.'" width="'.$width.'" height="'.$height.'" ';
    $out .= ($class) ? 'class="'.$class.'"' : '/>';

    return $out;

}


function oui_instagram_image_url($atts, $thing=null) {
    global $images, $image;

    extract(lAtts(array(
        'type'    => 'instagram',
        'class'    => 'oui_instagram_image_url',
        'link'    => 'auto',
    ),$atts));

    if (in_array($type, array('instagram', 'thumbnail', 'low_resolution', 'standard_resolution'))) {
        $url = ($type == 'instagram') ? $link = $image->{'link'} : $image->{'images'}->{$type}->{'url'};
        $link = ($link == 'auto') ? (($thing) ? 1 : 0) : $link;
        $out = ($thing) ? parse($thing) : $url;
        $out = ($link) ? href($out, $url, ' class="'.$class.'"') : $out;
        return $out;

    } else {
        trigger_error("unknown attribute value; oui_instagram_image_url type attribute accepts the following values: instagram, thumbnail, low_resolution, standard_resolution");
        return;
    }

}


function oui_instagram_image_info($atts) {
    global $images, $image;

    extract(lAtts(array(
        'wraptag'    => '',
        'class'    => '',
        'break' => '',
        'type'    => 'caption',
    ),$atts));
    
    $validItems = array('caption', 'likes', 'comments');
    $type = do_list($type);

    foreach ($type as $item) {
        $data = ($item=='caption') ? 'text' : 'count';
        if (in_array($item, $validItems)) {
                $out[] = $image->{$item}->{$data};
        }
    }

    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;

}

function oui_instagram_image_date($atts) {
    global $images, $image;

    extract(lAtts(array(
        'wraptag'      => '',
        'class'        => '',
        'format'    => '',
    ),$atts));

    $date = $image->{'caption'}->{'created_time'};

    $out = fileDownloadFormatTime(array(
        'ftime'  => $date,
        'format' => $format,
    ));
        
    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    
}

function oui_instagram_image_author($atts) {
    global $username, $images, $image;

    extract(lAtts(array(
        'wraptag'      => '',
        'class'        => '',
        'link'         => 0,
        'title'        => 1,
    ), $atts));

    $author_name = ($title) ? $image->{'user'}->{'username'} : $image->{'user'}->{'full_name'};
    $author = ($link) ? href($author_name, 'http://instagram.com/'.$username) : $author_name;

    return ($wraptag) ? doTag($author, $wraptag, $class) : $author;

}

# --- END PLUGIN CODE ---

?>