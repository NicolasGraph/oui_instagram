<?php

$plugin['name'] = 'oui_instagram';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.1.0';
$plugin['author'] = 'Nicolas Morand';
$plugin['author_uri'] = 'http://www.nicolasmorand.com';
$plugin['description'] = 'Instagram gallery';

$plugin['order'] = 5;

$plugin['type'] = 0;

// Plugin 'flags' signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use.
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

// $plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;
$plugin['flags'] = '0';

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
* "Tags":#tags
* "Exemples":#exemples
* "Author":#author
* "Licence":#licence

h2(#requirements). Plugin requirements

oui_disclaimer’s minimum requirements:

* Textpattern 4.5+

h2(#installation). Installation

Paste the content of the plugin file under the *Admin > Plugins*, upload it and install.

h2(#tags). Tags

h3. <txp:oui_instagram />

Displays a conditional warning message.
Should be placed in your each page, or in a form, depending on how it is used.

bc. <txp:oui_instagram />

h4. Attributes

If used as a single tag, @<txp:oui_disclaimer />@ should contains at least a @message@ attribute. 

* @username="…"@ - _Default: unset - The username of the Instagram account.

* @wraptag="…"@ - _Default: ul_ - The HTML tag used around the generated content.
* @class="…"@ – _Default: oui_instagram_ - The css class to apply to the HTML tag assigned to @wraptag@. 

* @break="…"@ - _Default: li_ - The HTML tag used around each generated image.

* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.

h2(#exemples). Exemples

h3. Exemple 1:

bc. <txp:oui_instagram username="fubiz" />

h2(#author). Author

"Nicolas Morand":http://www.nicolasmorand.com, from a "NOE interactive":http://noe-interactive.com "tip":http://noe-interactive.com/comment-integrer-ses-photos-instagram-sur-son-site.

h2(#licence). Licence

This plugin is distributed under "GPLv2":http://www.gnu.org/licenses/gpl-2.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---
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
    function __construct($username='',$access_token='') {
        if(empty($username) || empty($access_token)){
            $this->error('empty username or access token');
        } else {
            $this->username=$username;
            $this->access_token = $access_token;
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
                $this->error('getUserIDFromUserName');
            }
        } else {
            $this->error('empty username or access token');
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
            $this->error('empty username or access token');
        }
    }
    /*
     * Common mechanism to query the instagram api
     */
    public function queryInstagram($url){
        //prepare caching
        $cachefolder = __DIR__.'/';
        $cachekey = md5($url);
        $cachefile = $cachefolder.$cachekey.'_'.date('i').'.txt'; //cached for one minute
        //If not cached, -> instagram request
        if(!file_exists($cachefile)){
            //Request
            $request='error';
            if(!extension_loaded('openssl')){ $request = 'This class requires the php extension open_ssl to work as the instagram api works with httpS.'; }
            else { $request = file_get_contents($url); }
            //remove old caches
            $oldcaches = glob($cachefolder.$cachekey."*.txt");
            if(!empty($oldcaches)){foreach($oldcaches as $todel){
              unlink($todel);
            }}
            
            //Cache result
            $rh = fopen($cachefile,'w+');
            fwrite($rh,$request);
            fclose($rh);
        }
        //Execute and return query
        $query = json_decode(file_get_contents($cachefile));
        return $query;
    }
    /*
     * Error
     */
    public function error($src=''){
        echo '/!\ error '.$src.'. ';
    }
}






function oui_instagram($atts, $thing=null) {
    global $oui_disclaimer_urlvar, $oui_disclaimer_cookie, $oui_disclaimer_expires;

    extract(lAtts(array(
        'username'    => '',
        'count'  => '10',
        'size'    => 'thumbnail',
        'wraptag'     => 'ul',
        'class'       => 'oui_instagram',
        'break'       => 'li',
        'label'       => '',
        'labeltag'    => '',
    ),$atts));

    $access_token = '1517036843.ab103e5.2e484d7e57514253abb5d838d54511ca';

    if (!isset($atts['username'])) {
        trigger_error("oui_instagram requires a username attribute");
        return;
    }
    
	$isg = new instagramPhp($username,$access_token); //instanciates the class with the parameters
	$shots = $isg->getUserMedia(array('count'=>$count)); //Get the shots from instagram

    $out =  '<'.$wraptag.' class="'.$class.'">';
    
                foreach($shots->data as $istg){
                    //The image
                    $istg_thumbnail = $istg->{'images'}->{$size}->{'url'}; //Thumbnail
                    //If you want to display another size, you can use 'low_resolution', or 'standard_resolution' in place of 'thumbnail'
                    //The link
                    $istg_link = $istg->{'link'}; //Link to the picture's instagram page, to link to the picture image only, use $istg->{'images'}->{'standard_resolution'}->{'url'}
                    //The caption
                    $istg_caption = $istg->{'caption'}->{'text'};
                    //The markup
                    $out.='<'.$break.'><a rel="external" href="'.$istg_link.'"><img src="'.$istg_thumbnail.'" alt="'.$istg_caption.'" title="'.$istg_caption.'" /></a></'.$break.'>';
                }    
    
    
    return $out;

}
# --- END PLUGIN CODE ---

?>