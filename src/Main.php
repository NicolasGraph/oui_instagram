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

namespace Oui\Instagram {

    class Main
    {
        public $access_token;
        public $username;
        public $user_id;
        public $limit;

        private $api = 'https://api.instagram.com/v1/users/';

        /**
         * Get an Instagram user ID from a username.
         *
         * @param string $username Instagram username
         */
        public function getUserId($username)
        {
            // Search for the user id…
            $query = $this->api . $this->username . '&access_token=' . $this->access_token;
            $results = json_decode(file_get_contents($url));
            // …and check the result.
            if ($results->meta->code=='200') {
                foreach ($results->data as $user) {
                    if ($user->username == $username) {
                        $this->user_id = $user->id;
                    }
                }
            } else {
                trigger_error(
                    'oui_instagram was not able to get an Instagram user ID for '
                    . $username . '. ' . $results->meta->error_message
                );
            }
        }

        /**
         * Get the Instagram recent feed
         */
        public function getShots()
        {
            if (!$this->user_id && $this->username) {
                $this->getUserId($this->username);
            }

            $pages = ceil($this->limit / 20);
            $shots = array();

            for ($page = 1; $page <= $pages; $page++) {
                $count = ($page == $pages) ? ($this->limit % 20) : '20';
                $from = isset($next) ? $next : '';
                $query = $this->api . $this->user_id . '/media/recent?access_token=' . $this->access_token . '&count=' . $count . $from;

                $results = json_decode(file_get_contents($query));

                if ($page != $pages) {
                    $next = '&max_id=' . $results->{'pagination'}->{'next_max_id'};
                }

                if ($results->meta->code=='200') {
                    $shots = array_merge($shots, $results->data);
                } else {
                    trigger_error(
                        'oui_instagram was not able to get your ' . $this->limit . ' last shots. '
                        . $results->meta->error_message
                    );
                }
            }

            return $shots;
        }

        /**
         * Get a gallery from the recent shots.
         *
         * @param string $type Image resolution
         * @param string $link Images are linked to…
         */
        public function getGallery($type, $link)
        {
            $shots = $this->getShots();

            foreach ($shots as $shot) {
                $out[] = $this->getImage($shot, $type, $link);
            }

            return $out;
        }

        /**
         * Get a defined shot.
         *
         * @param object $shot shot related feed portion
         * @param string $type Image resolution
         * @param string $link Images are linked to…
         */
        public function getImage($shot, $type, $link)
        {
            $src = 'src="' . $shot->{'images'}->{$type}->{'url'}.'"';
            $width = 'width="' . $shot->{'images'}->{$type}->{'width'}.'"';
            $height = 'height="' . $shot->{'images'}->{$type}->{'height'}.'"';

            if (isset($shot->{'caption'}->{'text'})) {
                $alt = 'title="' . $shot->{'caption'}->{'text'}.'"';
            } else {
                $alt = '';
            }

            $title = $alt;
            $url = ($link == 'auto') ? $shot->{'link'} : $shot->{'images'}->{$type}->{'url'};
            $img = '<img ' . $src . $alt . $width . $height . ' />';

            return $link ? href($img, $url, $title) : $img;
        }

        /**
         * Get a defined shot url.
         *
         * @param object $shot shot related feed portion
         * @param string $type Image resolution
         */
        public function getImageUrl($shot, $type)
        {
            $validTypes = array('instagram', 'thumbnail', 'low_resolution', 'standard_resolution');

            if (in_array($type, $validTypes)) {
                $out = ($type == 'instagram') ? $shot->{'link'} : $shot->{'images'}->{$type}->{'url'};
                return $out;
            } else {
                trigger_error(
                    "unknown attribute value;
                    oui_instagram_image_url type attribute accepts the following values:
                    instagram, thumbnail, low_resolution, standard_resolution"
                );
                return;
            }
        }

        /**
         * Get infos about a defined shot.
         *
         * @param object $shot shot related feed portion
         * @param string $type 'caption', 'likes' or 'comments'
         */
        public function getImageInfo($shot, $type)
        {
            $validTypes = array('caption', 'likes', 'comments');
            $types = do_list($type);

            foreach ($types as $type) {
                $data = ($type=='caption') ? 'text' : 'count';
                if (in_array($type, $validTypes)) {
                    $out[] = isset($shot->{$type}->{$data}) ? $shot->{$type}->{$data} : '';
                }
            }

            return $out;
        }

        /**
         * Get the post date of a defined shot.
         *
         * @param object $shot   shot related feed portion
         * @param string $format Date format
         */
        public function getImageDate($shot, $format)
        {
            return fileDownloadFormatTime(array(
                'ftime'  => $shot->{'caption'}->{'created_time'},
                'format' => $format,
            ));
        }

        /**
         * Get defined shot author.
         *
         * @param object $shot  shot related feed portion
         * @param bool   $title Image resolution
         * @param bool   $link  Link to…
         */
        public function getImageAuthor($shot, $title, $link)
        {
            $author = ($title) ? $shot->{'user'}->{'username'} : $shot->{'user'}->{'full_name'};

            return $link ? href($author, 'http://instagram.com/'.$username) : $author;
        }
    }
}
