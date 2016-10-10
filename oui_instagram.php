<?php

/*
 * oui_instagram - Easily display recent images from an Instagram account.
 *
 * https://github.com/NicolasGraph/oui_instagram
 *
 * Copyright (C) 2016 Nicolas Morand
 *
 * This file is part of oui_instagram.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Register tags.
 */
Txp::get('\Textpattern\Tag\Registry')
    ->register('oui_instagram_images')
    ->register('oui_instagram_image')
    ->register('oui_instagram_image_info')
    ->register('oui_instagram_image_url')
    ->register('oui_instagram_image_date')
    ->register('oui_instagram_image_author');


/**
 * Register callbacks.
 */
if (txpinterface === 'admin') {
    add_privs('prefs.oui_instagram', '1');
    add_privs('plugin_prefs.oui_instagram', '1');

    register_callback('oui_instagram_welcome', 'plugin_lifecycle.oui_instagram');
    register_callback('oui_instagram_install', 'prefs', null, 1);
    register_callback('oui_instagram_options', 'plugin_prefs.oui_instagram', null, 1);
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
            remove_pref(null, 'oui_instagram');
            safe_delete('txp_lang', "owner LIKE 'oui\_instagram'");
            break;
    }
}

/**
 * Jump to the prefs panel.
 */
function oui_instagram_options()
{
    $url = '?event=prefs#prefs_group_oui_instagram';
    header('Location: ' . $url);
}

/**
 * Set prefs through:
 *
 */
function oui_instagram_preflist()
{
    $prefList = array(
        'oui_instagram_access_token' => array(
            'value'      => '',
            'event'      => 'oui_instagram',
            'visibility' => PREF_PLUGIN,
            'widget'     => 'text_input',
            'position'   => '10',
            'is_private' => false,
        ),
        'oui_instagram_cache_time' => array(
            'value'      => '5',
            'event'      => 'oui_instagram',
            'visibility' => PREF_PLUGIN,
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


function oui_instagram_install()
{
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
 * Main plugin function.
 *
 * Pull images if needed;
 * parse and cache the gallery;
 * display the content.
 */
function oui_instagram_images($atts, $thing = null)
{
    global $thisShot;

    extract(lAtts(array(
        'access_token' => '',
        'username'     => '',
        'user_id'      => '',
        'limit'        => '10',
        'type'         => 'thumbnail',
        'link'         => 'auto',
        'cache_time'   => '',
        'wraptag'      => 'ul',
        'class'        => 'oui_instagram_images',
        'break'        => 'li',
        'label'        => '',
        'labeltag'     => '',
    ), $atts));

    $access_token ?: $access_token = get_pref('oui_instagram_access_token');

    if (!$user_id && !$username) {
        $user_id = 'self';
    }

    if (!$cache_time) {
        $cache_time = get_pref('oui_instagram_cache_time');
    }

    // Prepare the cache file name.
    $keyBase = md5($username.$limit.$type.$thing);
    $hashKey = str_split(get_pref('oui_instagram_hash_key'));
    $cacheKey='';
    foreach ($hashKey as $hashSkip) {
        $cacheKey .= $keyBase[$hashSkip];
    }
    $cacheFile = find_temp_dir().DS.'oui_instagram_data_'.$cacheKey;
    $cacheExists = file_exists($cacheFile);
    $cacheSet = get_pref('oui_instagram_cache_set');

    // Main cache conditioning variable.
    $needQuery = (!$cacheExists || (time() - $cacheSet) > ($cache_time *  60)) ? true : false;

    if ($needQuery) {
        // New query needed.
        $api = 'https://api.instagram.com/v1/users/';

        if ($username) {
            // Search for the user id…
            $queryUrl = $api.$username.'&access_token='.$access_token;
            $user_idQuery = json_decode(file_get_contents($url));
            // …and check the result.
            if ($user_idQuery->meta->code=='200') {
                foreach ($user_idQuery->data as $user) {
                    if ($user->username == $username) {
                        $user_id = $user->id;
                    }
                }
            } else {
                trigger_error(
                    'oui_instagram was not able to get an Instagram user ID for '
                    .$username.'. '.$user_idQuery->meta->error_message
                );
            }
        }

        // Get the Instagram feed per 20 images because of the Instagram limit…
        $pagesCount = ceil($limit / 20);
        $shots = array();

        for ($page = 1; $page <= $pagesCount; $page++) {
            $count = ($page == $pagesCount) ? ($limit % 20) : '20';
            $from = isset($nextShots) ? $nextShots : '';
            $query = $api.$user_id.'/media/recent?access_token='.$access_token.'&count='.$count.$from;

            $shots[$page] = json_decode(file_get_contents($query));

            if ($page != $pagesCount) {
                $nextShots = '&max_id='.$shots[$page]->{'pagination'}->{'next_max_id'};
            }

            if (isset($shots[$page]->data)) {
                // Check the query result.
                foreach ($shots[$page]->data as $thisShot) {
                    if ($thing === null) {
                        // single tag use.
                        $src = 'src="'.$thisShot->{'images'}->{$type}->{'url'}.'"';
                        $width = 'width="'.$thisShot->{'images'}->{$type}->{'width'}.'"';
                        $height = 'height="'.$thisShot->{'images'}->{$type}->{'height'}.'"';
                        if (isset($thisShot->{'caption'}->{'text'})) {
                            $alt = 'title="'.$thisShot->{'caption'}->{'text'}.'"';
                        } else {
                            $alt = '';
                        }
                        $title = $alt;
                        $url = ($link == 'auto') ? $thisShot->{'link'} : $thisShot->{'images'}->{$type}->{'url'};

                        if ($link) {
                            $data[] = href('<img '. $src . $alt . $width . $height .' />', $url, $title);
                        } else {
                            $data[] = '<img '. $src . $alt . $width . $height .' />';
                        }

                        $out = (($label) ? doLabel($label, $labeltag) : '').\n
                               .doWrap($data, $wraptag, $break, $class);
                    } else {
                        // Container tag use.
                        $data[] = parse($thing);
                        $out = (($label) ? doLabel($label, $labeltag) : '').\n
                               .doWrap($data, $wraptag, $break, $class);
                    }
                }
            } else {
                trigger_error("oui_instagram was not able to get your account feed");
                return;
            }
        }
        update_lastmod();

        if ($cache_time > 0) {
            // Cache file needed.
            // Remove old cache files.
            $oldCaches = glob($cacheFile);
            if (!empty($oldCaches)) {
                foreach ($oldCaches as $toDelete) {
                    unlink($toDelete);
                }
            }
            // Time stamp and write the new cache files and return.
            set_pref('oui_instagram_cache_set', time());
            $cache = fopen($cacheFile, 'w+');
            fwrite($cache, $out);
            fclose($cache);
        }
    }

    if (!$needQuery && $cache_time > 0) {
        // Return the cache content or the generated images.
        $cacheOut = file_get_contents($cacheFile);
        return $cacheOut;
    } else {
        // …or the generated images.
        return $out;
    }
}

/**
 * Display each image in a oui_instagram_images context.
 */
function oui_instagram_image($atts)
{
    global $thisShot;

    extract(lAtts(array(
        'type'    => 'thumbnail',
        'class'   => '',
        'wraptag' => '',
    ), $atts));

    $src = 'src="'.$thisShot->{'images'}->{$type}->{'url'}.'"';
    $width = 'width="'.$thisShot->{'images'}->{$type}->{'width'}.'"';
    $height = 'height="'.$thisShot->{'images'}->{$type}->{'height'}.'"';
    if (isset($thisShot->{'caption'}->{'text'})) {
        $alt = 'title="'.$thisShot->{'caption'}->{'text'}.'"';
    } else {
        $alt = '';
    }

    $out = '<img '. $src . $alt . $width . $height;
    $out .= ($wraptag) ? '' : ($class) ? ' class="'.$class.'" />' : '/>';

    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
}

/**
 * Display each image url in a oui_instagram_images context.
 */
function oui_instagram_image_url($atts, $thing = null)
{
    global $thisShot;

    extract(lAtts(array(
        'type'    => 'instagram',
        'wraptag' => '',
        'class'   => '',
        'link'    => 'auto',
    ), $atts));

    $validTypes = array('instagram', 'thumbnail', 'low_resolution', 'standard_resolution');

    if (in_array($type, $validTypes)) {
        $url = ($type == 'instagram') ? $thisShot->{'link'} : $thisShot->{'images'}->{$type}->{'url'};
    } else {
        trigger_error(
            "unknown attribute value;
            oui_instagram_image_url type attribute accepts the following values:
            instagram, thumbnail, low_resolution, standard_resolution"
        );
        return;
    }

    $validLinks = array('auto', '1', '0');

    if (in_array($link, $validLinks)) {
        $link = ($link == 'auto') ? (($thing) ? 1 : 0) : $link;
        $out = ($thing) ? parse($thing) : $url;
        $out = ($link) ? href($out, $url, ($wraptag) ? '' : ' class="'.$class.'"') : $out;
        return doTag($out, $wraptag, $class);
    } else {
        trigger_error(
            "unknown attribute value;
            oui_instagram_image_url link attribute accepts the following values:
            auto, 1, 0"
        );
        return;
    }
}

/**
 * Display each image information in a oui_instagram_images context.
 */
function oui_instagram_image_info($atts)
{
    global $thisShot;

    extract(lAtts(array(
        'wraptag' => '',
        'class'   => '',
        'break'   => '',
        'type'    => 'caption',
    ), $atts));

    $validTypes = array('caption', 'likes', 'comments');
    $types = do_list($type);

    $out = array();
    foreach ($types as $type) {
        $data = ($type=='caption') ? 'text' : 'count';
        if (in_array($type, $validTypes)) {
            $out[] = isset($thisShot->{$type}->{$data}) ? $thisShot->{$type}->{$data} : '';
        }
    }

    return doWrap($out, $wraptag, $break, $class);
}

/**
 * Display each image date in a oui_instagram_images context.
 */
function oui_instagram_image_date($atts)
{
    global $thisShot;

    extract(lAtts(array(
        'wraptag' => '',
        'class'   => '',
        'format'  => '',
    ), $atts));

    $date = $thisShot->{'caption'}->{'created_time'};

    $out = fileDownloadFormatTime(array(
        'ftime'  => $date,
        'format' => $format,
    ));

    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
}

/**
 * Display each image author in a oui_instagram_images context.
 */
function oui_instagram_image_author($atts)
{
    global $thisShot;

    extract(lAtts(array(
        'wraptag' => '',
        'class'   => '',
        'link'    => 0,
        'title'   => 1,
    ), $atts));

    $author = ($title) ? $thisShot->{'user'}->{'username'} : $thisShot->{'user'}->{'full_name'};
    if ($link) {
        $out = href($author, 'http://instagram.com/'.$username, ($wraptag) ? '' : ' class="'.$class.'"');
    } else {
        $out = $author;
    }

    return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
}
