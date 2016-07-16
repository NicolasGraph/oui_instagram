<?php

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
    $url = defined('PREF_PLUGIN') ? '?event=prefs#prefs_group_oui_instagram' : '?event=prefs&step=advanced_prefs';
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
 * Pull images if needed;
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

                    // Conatainer tag use.
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
