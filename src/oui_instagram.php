<?php

$plugin['name'] = 'oui_instagram';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.5.0';
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
*IN PROCESS — This plugin is not released yet.*

h1. oui_instagram

Easily display recent images from an Instagram account.

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
* "Tags":#tags
* "Examples":#examples
* "Author":#author
* "Licence":#licence

h2(#requirements). Plugin requirements

oui_instagram’s minimum requirements:

* Textpattern 4.6+

h2(#installation). Installation

# Paste the content of the plugin file under the *Admin > Plugins*, upload it and install.

h2(#tags). Tags

h3. <txp:oui_instagram_images />

Displays a recent images list.

bc. <txp:oui_instagram />

h4. Attributes

@<txp:oui_instagram />@ should contains at least a @username@ attribute. 

* @username="…"@ - _Default: unset_ - The username of the Instagram account.
* @type="…"@ — _Default: thumbnail_ - The images size to use. Valid values are thumbnail, low_resolution, standard_resolution.
* @link="…"@ — _Default: auto_ - …
* @limit="…"@ — _Default: 10_ - The number of images to display.
* @cache_time="…"@ — _Default: 0_ - Duration of the cache in seconds.

* @wraptag="…"@ - _Default: ul_ - The HTML tag used around the generated content.
* @class="…"@ – _Default: oui_instagram_ - The css class to apply to the HTML tag assigned to @wraptag@. 

* @break="…"@ - _Default: li_ - The HTML tag used around each generated image.

* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.

h3. <txp:oui_instagram_image />

h4. Attributes

* @type="…"@ — 
* @class="…"@ — 
* @link="…"@ — 

h3. <txp:oui_instagram_image_info />

h4. Attributes

* @wraptag="…"@ — 
* @class="…"@ — 
* @break="…"@ — 
* @type="…"@ — 

h3. <txp:oui_instagram_image_url />

h4. Attributes

* @type="…"@ — 
* @class="…"@ — 
* @link="…"@ — 


h3. <txp:oui_instagram_image_date />

h4. Attributes

* @format="…"@ — 

h3. <txp:oui_instagram_image_author />

* @wraptag="…"@ — 
* @class="…"@ — 
* @title="…"@ — 
* @link="…"@ — 

h2(#examples). Examples

h3. Example 1:

bc. <txp:oui_instagram username="fubiz" />

h2(#author). Author

"Nicolas Morand":http://www.nicolasmorand.com, from a "NOE interactive":http://noe-interactive.com "tip":http://noe-interactive.com/comment-integrer-ses-photos-instagram-sur-son-site.

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
            $shots = $this->queryInstagram('https://api.instagram.com/v1/users/'.(int)$this->userid.'/media/recent?access_token='.$this->access_token.$qs); //Get shots
            if($shots->meta->code=='200'){
                return $shots;
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
        $cachefile = $cachefolder.'oui_instagram_data_'.$cachekey.'.txt'; //cached for one minute
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
    global $username, $shots, $shot;
    
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

    if(!empty($username) && !empty($access_token)){    
        $isg = new instagramPhp($username,$access_token,$cache_time); // instanciates the class with the parameters
        $shots = $isg->getUserMedia(array('count'=>$limit)); // Get the shots from instagram

        if(!empty($shots->data)){

            foreach($shots->data as $shot){
                if ($thing===null) {
                    $shot_image = $shot->{'images'}->{$type}->{'url'};
                    $shot_image_width = $shot->{'images'}->{$type}->{'width'};
                    $shot_image_height = $shot->{'images'}->{$type}->{'height'};
                    $shot_caption = $shot->{'caption'}->{'text'};
                    $shot_image_url = ($link == 'auto') ? $shot->{'link'} : $shot->{'images'}->{$type}->{'url'};

                    $out[]= ($link) ? href('<img class="'.$class.'" src="'.$shot_image.'" alt="'.$shot_caption.'" width="'.$shot_image_width.'" height="'.$shot_image_height.'" />',$shot_image_url, ' title="'.$shot_caption.'"') : '<img class="'.$class.'" src="'.$shot_image.'" alt="'.$shot_caption.'" width="'.$shot_image_width.'" height="'.$shot_image_height.'" />';
                    } else {
                    $out[]= parse($thing);
                    }
            }

            return doWrap($out, $wraptag, $break, $class);
            
        } else { 
            trigger_error("nothing to display; oui_instagram is unable to find any data to display.");
            return;         
        } 
        trigger_error("Missing required value(s); oui_instagram requires a username attribute and an access_token preference.");
        return;        
    }
     
}

function oui_instagram_image($atts) {
    global $shots, $shot;

    extract(lAtts(array(
        'type'    => 'thumbnail',
        'class'    => 'oui_instagram_image',
        'link'    => 'instagram',
    ),$atts));

    $shot_image = $shot->{'images'}->{$type}->{'url'};
    $shot_image_width = $shot->{'images'}->{$type}->{'width'};
    $shot_image_height = $shot->{'images'}->{$type}->{'height'};
    $shot_caption = $shot->{'caption'}->{'text'};
    
    $out = '<img class="'.$class.'" src="'.$shot_image.'" alt="'.$shot_caption.'" width="'.$shot_image_width.'" height="'.$shot_image_height.'" />';
    
    return $out;
    
}


function oui_instagram_image_url($atts, $thing=null) {
    global $shots, $shot;

    extract(lAtts(array(
        'type'    => 'instagram',
        'class'    => 'oui_instagram_image_url',
        'link'    => 'auto',
    ),$atts));

    if (in_array($type, array('instagram', 'thumbnail', 'low_resolution', 'standard_resolution'))) {
        $shot_image_url = ($type == 'instagram') ? $shot_link = $shot->{'link'} : $shot->{'images'}->{$type}->{'url'};
        $link = ($link == 'auto') ? (($thing) ? 1 : 0) : $link;
        $out = ($thing) ? parse($thing) : $shot_image_url;
        $out = ($link) ? href($out, $shot_image_url, ' class="'.$class.'"') : $out;
        return $out;

    } else {
        trigger_error("unknown attribute value; oui_instagram_image_url type attribute accepts the following values: instagram, thumbnail, low_resolution, standard_resolution");
        return;
    }
        
}


function oui_instagram_image_info($atts) {
    global $shots, $shot;

    extract(lAtts(array(
        'wraptag'    => 'p',
        'class'    => 'oui_instagram_image_info',
        'break' => br,
        'type'    => 'caption',
    ),$atts));
    
    $validItems = array('caption', 'likes', 'comments');
    $type = do_list($type);

    foreach ($type as $item) {
        $data = ($item=='caption') ? 'text' : 'count';
        if (in_array($item, $validItems)) {
                $out[] = $shot->{$item}->{$data};
        }
    }

return doWrap($out, $wraptag, $break, $class);

}

function oui_instagram_image_date($atts) {
    global $shots, $shot;

    extract(lAtts(array(
        'format'    => '',
    ),$atts));

    $shot_date = $shot->{'caption'}->{'created_time'};

    $out = fileDownloadFormatTime(array(
        'ftime'  => $shot_date,
        'format' => $format,
    ));
        
    return $out;
    
}

function oui_instagram_image_author($atts) {
    global $username, $shots, $shot;

    extract(lAtts(array(
        'class'        => 'oui_instagram_image_author',
        'link'         => 0,
        'title'        => 1,
        'wraptag'      => '',
    ), $atts));

    $author_name = ($title) ? $shot->{'user'}->{'username'} : $shot->{'user'}->{'full_name'};

    $author = ($link)
        ? href($author_name, 'http://instagram.com/'.$username)
        : $author_name;

    return ($wraptag) ? doTag($author, $wraptag, $class) : $author;
    
}

# --- END PLUGIN CODE ---

?>