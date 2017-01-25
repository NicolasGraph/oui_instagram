<?php

/*
 * oui_insta - Easily display recent images from an Instagram account.
 *
 * https://github.com/NicolasGraph/oui_insta
 *
 * Copyright (C) 2016 Nicolas Morand
 *
 * This file is part of oui_insta.
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
    function oui_insta_images($atts, $thing = null)
    {
        global $oui_insta_shot, $oui_insta_type;

        extract(lAtts(array(
            'access_token' => get_pref('oui_insta_access_token'),
            'limit'        => '10',
            'form'         => '',
            'type'         => 'thumbnail',
            'link'         => 'auto',
            'wraptag'      => 'ul',
            'class'        => '',
            'break'        => 'li',
            'label'        => '',
            'labeltag'     => '',
        ), $atts));

        $insta = 'Oui\Instagram\Main';
        $obj = new $insta;
        $obj->access_token = $access_token;
        $obj->limit = $limit;

        // Check the query result.
        if ($thing || $form) {
            $shots = $obj->getFeed();
            if ($shots) {
                foreach ($shots as $shot) {
                    $oui_insta_shot = $shot;
                    $oui_insta_type = $type;
                    $out[] = $thing ? parse($thing) : parse_form($form);
                }
                unset(
                    $GLOBALS['oui_insta_shot'],
                    $GLOBALS['oui_insta_type']
                );
            } else {
                trigger_error('oui_insta was not able to get your Instagram feed.');
                return;
            }
        } else {
            $images = $obj->getImages($type, $link);
            if ($images) {
                $out = $images;
            } else {
                trigger_error('oui_insta was not able to get your Instagram images.');
                return;
            }
        }

        $out = doLabel($label, $labeltag).doWrap($out, $wraptag, $break, $class);

        update_lastmod();

        return $out;
    }

    // Deprecated
    function oui_instagram_images($atts, $thing = null)
    {
        return oui_insta_images($atts, $thing);
    }

    /**
     * Display each image in a oui_insta_images context.
     */
    function oui_insta_image($atts)
    {
        global $oui_insta_shot, $oui_insta_type;

        extract(lAtts(array(
            'link'    => 'auto',
            'type'    => $oui_insta_type,
            'class'   => '',
            'wraptag' => '',
        ), $atts));

        $out = Oui\Instagram\Main::getImage($oui_insta_shot, $type, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    // Deprecated
    function oui_instagram_image($atts)
    {
        return oui_insta_image($atts);
    }

    /**
     * Display each image url in a oui_insta_images context.
     */
    function oui_insta_image_url($atts, $thing = null)
    {
        global $oui_insta_shot;

        extract(lAtts(array(
            'type'    => 'instagram',
            'wraptag' => '',
            'class'   => '',
            'link'    => 'auto',
        ), $atts));

        $url = Oui\Instagram\Main::getImageUrl($oui_insta_shot, $type, $link);

        $validLinks = array('auto', '1', '0');

        if (in_array($link, $validLinks)) {
            $link = ($link == 'auto') ? (($thing) ? 1 : 0) : $link;
            $out = ($thing) ? parse($thing) : $url;
            $out = ($link) ? href($out, $url, ($wraptag) ? '' : ' class="'.$class.'"') : $out;
        } else {
            trigger_error(
                "unknown attribute value;
                oui_insta_image_url link attribute accepts the following values:
                auto, 1, 0"
            );
        }

        return $out ? doTag($out, $wraptag, $class) : '';
    }

    // Deprecated
    function oui_instagram_image_url($atts, $thing = null)
    {
        return oui_insta_image_url($atts, $thing);
    }

    /**
     * Display each image information in a oui_insta_images context.
     */
    function oui_insta_image_info($atts)
    {
        global $oui_insta_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'type'    => 'caption',
        ), $atts));

        $out = Oui\Instagram\Main::getImageInfo($oui_insta_shot, $type);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    // Deprecated
    function oui_instagram_image_info($atts)
    {
        return oui_insta_image_info($atts);
    }

    /**
     * Display each image date in a oui_insta_images context.
     */
    function oui_insta_image_date($atts)
    {
        global $oui_insta_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'format'  => '',
        ), $atts));

        $out = Oui\Instagram\Main::getImageDate($oui_insta_shot, $format);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    // Deprecated
    function oui_instagram_image_date($atts)
    {
        return oui_insta_image_date($atts);
    }

    /**
     * Display each image author in a oui_insta_images context.
     */
    function oui_insta_image_author($atts)
    {
        global $oui_insta_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'link'    => 0,
            'type'   => 'username',
        ), $atts));

        $out = Oui\Instagram\Main::getImageAuthor($oui_insta_shot, $type, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    // Deprecated
    function oui_instagram_image_author($atts)
    {
        return oui_insta_image_author($atts);
    }

    /**
     * Main plugin function.
     *
     * Pull images if needed;
     * parse and cache the gallery;
     * display the content.
     */
    function oui_insta_author($atts, $thing = null)
    {
        global $oui_insta_profile;

        extract(lAtts(array(
            'access_token' => '',
            'type'         => 'username',
            'link'         => 'instagram',
            'wraptag'      => 'p',
            'form'         => '',
            'class'        => '',
            'break'        => 'br',
            'label'        => '',
            'labeltag'     => '',
        ), $atts));

        $access_token ?: $access_token = get_pref('oui_insta_access_token');

        $insta = 'Oui\Instagram\Main';
        $obj = new $insta;
        $obj->access_token = $access_token;

        // Check the query result.
        if ($thing || $form) {
            $profile = $obj->getProfile();
            if ($profile) {
                $oui_insta_profile = $profile;
                $out[] = $thing ? parse($thing) : $parse_form($form);
                unset($GLOBALS['oui_insta_profile']);
            } else {
                trigger_error('oui_insta was not able to get your Instagram feed.');
                return;
            }
        } else {
            $info = $obj->getAuthor($type, $link);
            if ($info) {
                $out[] = $info;
            } else {
                trigger_error('oui_insta was not able to get your Instagram images.');
                return;
            }
        }

        $out = doLabel($label, $labeltag).doWrap($out, $wraptag, $break, $class);

        update_lastmod();

        return $out;
    }

    // Deprecated
    function oui_instagram_author($atts, $thing = null)
    {
        return oui_insta_author($atts, $thing);
    }

    /**
     * Display each image information in a oui_insta_images context.
     */
    function oui_insta_author_info($atts)
    {
        global $oui_insta_profile;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'type'    => 'username',
            'link'    => 'instagram',
        ), $atts));

        $out = Oui\Instagram\Main::getAuthorInfo($oui_insta_profile, $type, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    // Deprecated
    function oui_instagram_author_info($atts, $thing = null)
    {
        return oui_insta_author_info($atts, $thing);
    }
}
