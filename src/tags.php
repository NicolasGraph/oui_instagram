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

namespace {

    /**
     * Main plugin function.
     *
     * Pull images if needed;
     * parse and cache the gallery;
     * display the content.
     */
    function oui_instagram_images($atts, $thing = null)
    {
        global $oui_instagram_obj, $oui_instagram_shot;

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
            $class = 'Oui\Instagram\Main';
            $obj = new $class;
            $obj->access_token = $access_token;
            $obj->user_id = $user_id;
            $obj->username = $username;
            $obj->limit = $limit;

            // Check the query result.
            if ($thing === null) {
                $out = $obj->getGallery($type, $link);
            } else {
                $shots = $obj->getShots();
                foreach ($shots as $shot) {
                    $oui_instagram_obj = $obj;
                    $oui_instagram_shot = $shot;
                    $out[] = parse($thing);
                }
                unset(
                    $GLOBALS['oui_instagram_obj'],
                    $GLOBALS['oui_instagram_shot']
                );
            }

            $out = (($label) ? doLabel($label, $labeltag) : '') . \n
                 . doWrap($out, $wraptag, $break, $class);

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
        global $oui_instagram_obj, $oui_instagram_shot;

        extract(lAtts(array(
            'link'    => 'auto',
            'type'    => 'thumbnail',
            'class'   => '',
            'wraptag' => '',
        ), $atts));

        $out = $oui_instagram_obj->getImage($oui_instagram_shot, $type, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    /**
     * Display each image url in a oui_instagram_images context.
     */
    function oui_instagram_image_url($atts, $thing = null)
    {
        global $oui_instagram_obj, $oui_instagram_shot;

        extract(lAtts(array(
            'type'    => 'instagram',
            'wraptag' => '',
            'class'   => '',
            'link'    => 'auto',
        ), $atts));

        $url = $oui_instagram_obj->getImageUrl($oui_instagram_shot, $type, $link);

        $validLinks = array('auto', '1', '0');

        if (in_array($link, $validLinks)) {
            $link = ($link == 'auto') ? (($thing) ? 1 : 0) : $link;
            $out = ($thing) ? parse($thing) : $url;
            $out = ($link) ? href($out, $url, ($wraptag) ? '' : ' class="'.$class.'"') : $out;
        } else {
            trigger_error(
                "unknown attribute value;
                oui_instagram_image_url link attribute accepts the following values:
                auto, 1, 0"
            );
        }

        return $out ? doTag($out, $wraptag, $class) : '';
    }

    /**
     * Display each image information in a oui_instagram_images context.
     */
    function oui_instagram_image_info($atts)
    {
        global $oui_instagram_obj, $oui_instagram_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'break'   => '',
            'type'    => 'caption',
        ), $atts));

        $out = $oui_instagram_obj->getImageInfo($oui_instagram_shot, $type);

        return doWrap($out, $wraptag, $break, $class);
    }

    /**
     * Display each image date in a oui_instagram_images context.
     */
    function oui_instagram_image_date($atts)
    {
        global $oui_instagram_obj, $oui_instagram_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'format'  => '',
        ), $atts));

        $out = $oui_instagram_obj->getImageDate($oui_instagram_shot, $format);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    /**
     * Display each image author in a oui_instagram_images context.
     */
    function oui_instagram_image_author($atts)
    {
        global $oui_instagram_obj, $oui_instagram_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'link'    => 0,
            'title'   => 1,
        ), $atts));

        $out = $oui_instagram_obj->getImageAuthor($oui_instagram_shot, $title, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }
}
