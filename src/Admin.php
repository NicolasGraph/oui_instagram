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

namespace Oui\Instagram {

    class Admin
    {

        public function __construct()
        {
            /**
             * Register callbacks.
             */
            if (txpinterface === 'admin') {
                add_privs('prefs.oui_instagram', '1');
                add_privs('plugin_prefs.oui_instagram', '1');

                register_callback(array($this, 'lifeCycle'), 'plugin_lifecycle.oui_instagram');
                register_callback(array($this, 'setPref'), 'prefs', null, 1);
                register_callback(array($this, 'options'), 'plugin_prefs.oui_instagram', null, 1);
            } else {
                /**
                 * Register tags.
                 */
                \Txp::get('\Textpattern\Tag\Registry')
                    ->register('oui_insta_author')
                    ->register('oui_insta_author_info')
                    ->register('oui_insta_images')
                    ->register('oui_insta_image')
                    ->register('oui_insta_image_info')
                    ->register('oui_insta_image_url')
                    ->register('oui_insta_image_date')
                    ->register('oui_insta_image_author');
            }
        }

        /**
         * Handler for plugin lifecycle events.
         *
         * @param string $evt Textpattern action event
         * @param string $stp Textpattern action step
         */
        public function lifeCycle($evt, $stp)
        {
            switch ($stp) {
                case 'enabled':
                    $this->setPref();
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
        public function options()
        {
            $url = '?event=prefs#prefs_group_oui_instagram';
            header('Location: ' . $url);
        }

        /**
         * Set the access token pref.
         */
        public function setPref()
        {
            $pref = 'oui_instagram_access_token';
            if (get_pref($pref, null) === null) {
                set_pref($pref, '', 'oui_instagram', PREF_PLUGIN, 'text_input');
            }
        }
    }

    new Admin;

}
