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

    class Main
    {
        public $access_token;
        public $limit = 10;

        private $api = 'https://api.instagram.com/v1/users/self/';

        public function __construct()
        {
            $this->access_token = get_pref('oui_instagram_access_token');
        }
        /**
         * Get the Instagram recent feed
         */
        public function getProfile()
        {
            if ($this->access_token) {
                $query = $this->api . '?access_token=' . $this->access_token;

                if (function_exists('curl_version')) {
                    $ch = curl_init($query);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = json_decode(curl_exec($ch));
                } else {
                    $result = json_decode(file_get_contents($query));
                }

                if ($result->meta->code=='200') {
                    return $result->data;
                } else {
                    trigger_error('oui_insta was not able to get your profile.');
                    return;
                }
            } else {
                trigger_error('oui_insta is missing an Instagram access token.');
                return;
            }
        }

        /**
         * Get infos about a defined shot.
         *
         * @param object $shot shot related feed portion
         * @param string $type 'caption', 'likes' or 'comments'
         */
        public function getAuthor($type = 'username', $link = 'instagram')
        {
            $profile = $this->getProfile();
            return self::getAuthorInfo($profile, $type, $link);
        }

        /**
         * Get infos about a defined shot.
         *
         * @param object $shot shot related feed portion
         * @param string $type 'caption', 'likes' or 'comments'
         */
        public static function getAuthorInfo($profile, $type = 'username', $link = 'instagram')
        {
            $infos = array(
                'id'              => $profile->{'id'},
                'username'        => $profile->{'username'},
                'avatar'          => $profile->{'profile_picture'},
                'full_name'       => $profile->{'full_name'},
                'bio'             => $profile->{'bio'},
                'website'         => $profile->{'website'},
                'followers_count' => $profile->{'counts'}->{'followed_by'},
                'following_count' => $profile->{'counts'}->{'follows'},
                'medias_count'    => $profile->{'counts'}->{'media'},
            );

            if (array_key_exists($type, $infos)) {
                $info = $infos[$type];
            } else {
                trigger_error('Unknown author info.');
            }

            if ($type === 'avatar') {
                $info = '<img src="' . $info . '" width="150" height="150" alt="' . $profile->{'username'} . '" />';
            }

            if ($link === 'instagram') {
                $username = $profile->{'username'};
                $url = 'https://www.instagram.com/' . $username;
                $title = $username . gtxt('on_instagram');
            } elseif ($link === 'website') {
                $username = $profile->{'username'};
                $url = $profile->{'website'};
                $title = $username . gtxt('s_website');
            }

            return $link ? href($info, $url, $title) : $img;
        }

        /**
         * Get the Instagram recent feed
         */
        public function getFeed()
        {
            if ($this->access_token) {
                $pages = ceil($this->limit / 20);
                $shots = array();

                for ($page = 1; $page <= $pages; $page++) {
                    $count = ($page == $pages) ? ($this->limit % 20) : '20';
                    $from = isset($next) ? $next : '';
                    $query = $this->api . 'media/recent?access_token=' . $this->access_token . '&count=' . $count . $from;

                    if (function_exists('curl_version')) {
                        $ch = curl_init($query);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $results = json_decode(curl_exec($ch));
                    } else {
                        $results = json_decode(file_get_contents($query));
                    }

                    if ($page != $pages) {
                        $next = '&max_id=' . $results->{'pagination'}->{'next_max_id'};
                    }

                    if ($results->meta->code=='200') {
                        $shots = array_merge($shots, $results->data);
                    } else {
                        trigger_error(
                            'oui_insta was not able to get your ' . $this->limit . ' last shots. '
                            . $results->meta->error_message
                        );
                    }
                }
                return $shots;
            } else {
                trigger_error('oui_insta is missing an Instagram access token.');
                return;
            }
        }

        /**
         * Get a gallery from the recent shots.
         *
         * @param string $type Image resolution
         * @param string $link Images are linked to…
         */
        public function getImages($type = 'thumbnail', $link = 'auto')
        {
            if ($this->access_token) {
                $shots = $this->getfeed();
                if ($shots) {
                    foreach ($shots as $shot) {
                        $out[] = self::getImage($shot, $type, $link);
                    }
                } else {
                    trigger_error('oui_insta was not able to get your Instagram feed.');
                    return;
                }
                return $out;
            } else {
                trigger_error('oui_insta is missing an Instagram access token.');
                return;
            }
        }

        /**
         * Get a defined shot.
         *
         * @param object $shot shot related feed portion
         * @param string $type Image resolution
         * @param string $link Images are linked to…
         */
        public static function getImage($shot, $type = 'thumbnail', $link = 'auto')
        {
            $src = 'src="' . $shot->{'images'}->{$type}->{'url'}.'" ';
            $width = 'width="' . $shot->{'images'}->{$type}->{'width'}.'" ';
            $height = 'height="' . $shot->{'images'}->{$type}->{'height'}.'" ';

            if (isset($shot->{'caption'}->{'text'})) {
                $alt = 'title="' . $shot->{'caption'}->{'text'}.'" ';
            } else {
                $alt = ' ';
            }

            $title = $alt;
            $url = ($link === 'auto') ? $shot->{'link'} : $shot->{'images'}->{$type}->{'url'};
            $img = '<img ' . $src . $alt . $width . $height . '/>';

            return $link ? href($img, $url, $title) : $img;
        }

        /**
         * Get a defined shot url.
         *
         * @param object $shot shot related feed portion
         * @param string $type Image resolution
         */
        public static function getImageUrl($shot, $type = 'thumbnail')
        {
            $validTypes = array('instagram', 'thumbnail', 'low_resolution', 'standard_resolution');

            if (in_array($type, $validTypes)) {
                $out = ($type == 'instagram') ? $shot->{'link'} : $shot->{'images'}->{$type}->{'url'};
                return $out;
            } else {
                trigger_error(
                    "unknown attribute value;
                    oui_insta_image_url type attribute accepts the following values:
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
        public static function getImageInfo($shot, $type = 'caption')
        {
            $infos = array(
                'id'             => $shot->{'id'},
                'type'           => $shot->{'type'},
                'date'           => $shot->{'created_time'},
                'w'              => $shot->{'images'}->{'standard_resolution'}->{'width'},
                'h'              => $shot->{'images'}->{'standard_resolution'}->{'height'},
                'thumb_w'        => $shot->{'images'}->{'thumbnail'}->{'width'},
                'thumb_h'        => $shot->{'images'}->{'low_resolution'}->{'height'},
                'low_w'          => $shot->{'images'}->{'low_resolution'}->{'width'},
                'low_h'          => $shot->{'images'}->{'thumbnail'}->{'height'},
                'comments'       => $shot->{'comments'}->{'count'},
                'comments_count' => $shot->{'comments'}->{'count'},
                'likes'          => $shot->{'likes'}->{'count'},
                'likes_count'    => $shot->{'likes'}->{'count'},
                'caption'        => isset($shot->{'caption'}->{'text'}) ? $shot->{'caption'}->{'text'} : '',
                'location'       => isset($shot->{'location'}->{'name'}) ? $shot->{'location'}->{'name'} : '',
                'address'        => isset($shot->{'location'}->{'street_address'}) ? $shot->{'location'}->{'street_address'} : '',
                'latitude'       => isset($shot->{'location'}->{'latitude'}) ? $shot->{'location'}->{'latitude'} : '',
                'longitude'      => isset($shot->{'location'}->{'longitude'}) ? $shot->{'location'}->{'longitude'} : '',
            );

            if (array_key_exists($type, $infos)) {
                return $infos[$type];
            } else {
                trigger_error('Unknown image info.');
            }
        }

        /**
         * Get the post date of a defined shot.
         *
         * @param object $shot   shot related feed portion
         * @param string $format Date format
         */
        public static function getImageDate($shot, $format = '')
        {
            return fileDownloadFormatTime(array(
                'ftime'  => $shot->{'created_time'},
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
        public static function getImageAuthor($shot, $type = 'username', $link = 0)
        {
            $infos = array(
                'username' => $shot->{'user'}->{'username'},
                'id'       => $shot->{'user'}->{'id'},
                'avatar'   => $shot->{'user'}->{'profile_picture'},
            );

            if (array_key_exists($type, $infos)) {
                $author = $infos[$type];
            } else {
                trigger_error('Unknown image info.');
            }

            if ($type === 'avatar') {
                $alt = $shot->{'user'}->{'username'};
                $author = '<img src="' . $author . '" width="150" height="150" alt=" alt="' . $alt . '" />';
            }

            if ($author) {
                return $link ? href($author, 'http://instagram.com/'.$username) : $author;
            } else {
                trigger_error('Unknown image info.');
            }
        }
    }
}
