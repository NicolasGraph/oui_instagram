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
                add_privs('prefs.oui_insta', '1');
                add_privs('plugin_prefs.oui_insta', '1');

                register_callback(array($this, 'lifeCycle'), 'plugin_lifecycle.oui_insta');
                register_callback(array($this, 'setPrefs'), 'prefs', null, 1);
                register_callback(array($this, 'options'), 'plugin_prefs.oui_insta', null, 1);
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
                    $this->setPrefs();
                    break;
                case 'deleted':
                    remove_pref(null, 'oui_insta');
                    safe_delete('txp_lang', "owner LIKE 'oui\_instagram'");
                    break;
            }
        }

        /**
         * Jump to the prefs panel.
         */
        public function options()
        {
            $url = '?event=prefs#prefs_group_oui_insta';
            header('Location: ' . $url);
        }

        /**
         * Set prefs through:
         *
         */
        public function getPrefs()
        {
            $prefList = array(
                'oui_insta_access_token' => array(
                    'value'      => '',
                    'visibility' => PREF_PLUGIN,
                    'widget'     => 'text_input',
                ),
            );
            return $prefList;
        }


        public function setPrefs()
        {
            $prefList = $this->getPrefs();

            foreach ($prefList as $pref => $options) {
                $position = 250;
                if (get_pref($pref, null) === null) {
                    set_pref(
                        $pref,
                        $options['value'],
                        'oui_insta',
                        $options['visibility'],
                        $options['widget'],
                        $position
                    );
                }
                $position++;
            }
        }
    }

    new Admin;

}
