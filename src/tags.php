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
        global $oui_instagram_shot, $oui_instagram_type;

        extract(lAtts(array(
            'access_token' => get_pref('oui_instagram_access_token'),
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

        $class = 'Oui\Instagram\Main';
        $obj = new $class;
        $obj->access_token = $access_token;
        $obj->limit = $limit;

        // Check the query result.
        if ($thing || $form) {
            $shots = $obj->getFeed();
            if ($shots) {
                foreach ($shots as $shot) {
                    $oui_instagram_shot = $shot;
                    $oui_instagram_type = $type;
                    $out[] = $thing ? parse($thing) : parse_form($form);
                }
                unset(
                    $GLOBALS['oui_instagram_shot'],
                    $GLOBALS['oui_instagram_type']
                );
            } else {
                trigger_error('oui_instagram was not able to get your Instagram feed.');
                return;
            }
        } else {
            $images = $obj->getImages($type, $link);
            if ($images) {
                $out = $images;
            } else {
                trigger_error('oui_instagram was not able to get your Instagram images.');
                return;
            }
        }

        $out = doLabel($label, $labeltag).doWrap($out, $wraptag, $break, $class);

        update_lastmod();

        return $out;
    }

    /**
     * Display each image in a oui_instagram_images context.
     */
    function oui_instagram_image($atts)
    {
        global $oui_instagram_shot, $oui_instagram_type;

        extract(lAtts(array(
            'link'    => 'auto',
            'type'    => $oui_instagram_type,
            'class'   => '',
            'wraptag' => '',
        ), $atts));

        $out = Oui\Instagram\Main::getImage($oui_instagram_shot, $type, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    /**
     * Display each image url in a oui_instagram_images context.
     */
    function oui_instagram_image_url($atts, $thing = null)
    {
        global $oui_instagram_shot;

        extract(lAtts(array(
            'type'    => 'instagram',
            'wraptag' => '',
            'class'   => '',
            'link'    => 'auto',
        ), $atts));

        $url = Oui\Instagram\Main::getImageUrl($oui_instagram_shot, $type, $link);

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
        global $oui_instagram_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'type'    => 'caption',
        ), $atts));

        $out = Oui\Instagram\Main::getImageInfo($oui_instagram_shot, $type);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    /**
     * Display each image date in a oui_instagram_images context.
     */
    function oui_instagram_image_date($atts)
    {
        global $oui_instagram_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'format'  => '',
        ), $atts));

        $out = Oui\Instagram\Main::getImageDate($oui_instagram_shot, $format);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    /**
     * Display each image author in a oui_instagram_images context.
     */
    function oui_instagram_image_author($atts)
    {
        global $oui_instagram_shot;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'link'    => 0,
            'type'   => 'username',
        ), $atts));

        $out = Oui\Instagram\Main::getImageAuthor($oui_instagram_shot, $type, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }

    /**
     * Main plugin function.
     *
     * Pull images if needed;
     * parse and cache the gallery;
     * display the content.
     */
    function oui_instagram_author($atts, $thing = null)
    {
        global $oui_instagram_profile;

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

        $access_token ?: $access_token = get_pref('oui_instagram_access_token');

        $class = 'Oui\Instagram\Main';
        $obj = new $class;
        $obj->access_token = $access_token;

        // Check the query result.
        if ($thing || $form) {
            $profile = $obj->getProfile();
            if ($profile) {
                $oui_instagram_profile = $profile;
                $out[] = $thing ? parse($thing) : $parse_form($form);
                unset($GLOBALS['oui_instagram_profile']);
            } else {
                trigger_error('oui_instagram was not able to get your Instagram feed.');
                return;
            }
        } else {
            $info = $obj->getAuthor($type, $link);
            if ($info) {
                $out[] = $info;
            } else {
                trigger_error('oui_instagram was not able to get your Instagram images.');
                return;
            }
        }

        $out = doLabel($label, $labeltag).doWrap($out, $wraptag, $break, $class);

        update_lastmod();

        return $out;
    }

    /**
     * Display each image information in a oui_instagram_images context.
     */
    function oui_instagram_author_info($atts)
    {
        global $oui_instagram_profile;

        extract(lAtts(array(
            'wraptag' => '',
            'class'   => '',
            'type'    => 'username',
            'link'    => 'instagram',
        ), $atts));

        $out = Oui\Instagram\Main::getAuthorInfo($oui_instagram_profile, $type, $link);

        return ($wraptag) ? doTag($out, $wraptag, $class) : $out;
    }
}
