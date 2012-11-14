<?php

/**
 * Yii Framework Widget to embed Soundcloud html5 Player into Yii Framework web apps.
 *
 * @author      Nelson J Morais <njmorais@gmail.com>
 * @copyright   2012 Nelson J Morais <njmorais@gmail.com>
 * @license     http://opensource.org/licenses/BSD-3-Clause
 * @link        http://github.com/njasm/Yii-Soundcloud-Player-Widget
 * @category    Web Services
 * @package     Soundcloud Player Widget
 * @version     0.0.3
 */

/**
 * Copyright (c) 2012, Nelson J Morais
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that 
 * the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of conditions and the 
 * following disclaimer.
 * 
 * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the 
 * following disclaimer in the documentation and/or other materials provided with the distribution.
 * 
 * Neither the name of the Nelson J Morais nor the names of its contributors may be used to endorse or promote 
 * products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, 
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR 
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class yiiSoundcloudPlayerWidget extends CWidget
{
    /**
     * cURL Handler
     * @var Object
     * @access private
     */
    private $_curl;

    /**
     * cURL response data
     * @var Object
     * @access private
     */
    private $_scResponse;

    /**
     * Paramater for development. if set to true will echo out curl and soundcloud api http errors, if any.
     * @var boolean
     * @access public
     */
    public $devel = true;

    /**
     * Cache data. for high-traffic web apps and Soundcloud api politeness
     * @var boolean
     * @access public
     */
    public $cache = true;

    /**
     * Cache time. Time to keep data in cache server before expire
     * @var integer
     * @access public
     */
    public $cacheTime = 300; // 5 minutes to keep cache $id in cache server.

    /**
     * SOUNDCLOUD DEFINITION: A Soundcloud URL for a track, set, group, user, etc.
     * @access public
     */
    public $url;

    /**
     * SOUNDCLOUD DEFINITION: (optional) Either xml, json or js (for jsonp). 
     * Since we're only echoing the iframe code received, the developer doesn't need to access this parameter.
     * @var string
     * @access private
     */
    private $_format = 'json';

    /**
     * SOUNDCLOUD DEFINITION: (optional) The maximum width in px.
     * @var integer
     * @access public
     */
    public $maxwidth;

    /**
     * SOUNDCLOUD DEFINITION: (optional) The maximum height in px. The default is 81 for tracks and 305 for all other.
     * @var integer
     * @access public
     */
    public $maxheight;

    /**
     * SOUNDCLOUD DEFINITION: (optional) The primary color of the widget as a hex triplet. (For example: ff0066).
     * @var string
     * @access public
     */
    public $color;

    /**
     * SOUNDCLOUD DEFINITION: (optional) Whether the widget plays on load.
     * @var boolean
     * @access public
     */
    public $auto_play = false;

    /**
     * SOUNDCLOUD DEFINITION: (optional) Whether the player displays timed comments.
     * @var boolean
     * @access public
     */
    public $show_comments = true;

    /**
     * SOUNDCLOUD DEFINITION: (optional) Whether the new HTML5 Iframe-based Widget or the old Adobe Flash 
     * Widget will be returned.
     * @var boolean
     * @access public
     */
    public $iframe = true;

    public function run()
    {
        if (!extension_loaded('curl')) {
            if ($this->devel == true) {
                echo '<p>We need cURL extension loaded in php to be able to run. today is a sad day ;(</p>';
            }
            return;
        }
        if (!function_exists('json_decode')) {
            if ($this->devel == true) {
                echo '<p>We need json extension :(</p>';
            }
            return;
        }

        if (empty($this->url)) {
            if ($this->devel == true) {
                echo '<p>No "url" parameter.</p>';
            }
            return;
        }

        if (is_array($this->url)) {
            foreach ($this->url as $url) {
                echo $this->_getData($url);
            }
            return;
        }

        if (is_string($this->url)) {
            echo $this->_getData($this->url);
        } else {
            if ($this->devel == true) {
                echo 'url should be string or array and is ' . gettype($this->url);
            }
        }
            
    }

    /**
     * Method to start querying soundcloud oembed for iframe data
     * @access private
     * @return string iframe from soundcloud oembed
     */
    private function _getData($url)
    {
        if ((Yii::app()->cache != null) && ($this->cache == true)) {
            $cachedUrl = Yii::app()->cache->get('yiiScUrl' . $url);
            if ($cachedUrl == false) {
                $this->_buildCurl($this->_buildURL($url));

                if ($this->_scResponse->info->http_code == 200) {
                    Yii::app()->cache->set('yiiScUrl' . $url, $this->_scResponse->data->html, $this->cacheTime);
                    return $this->_scResponse->data->html;
                } else {
                    echo ($this->devel == true) ? $this->_httpError($this->_scResponse->info->http_code, $url) : '';
                }
            } else {
                return $cachedUrl;
            }
        } else {
            $this->_buildCurl($this->_buildURL($url));

            if ($this->_scResponse->info->http_code == 200) {
                return $this->_scResponse->data->html;
            } else {
                echo ($this->devel == true) ? $this->_httpError($this->_scResponse->info->http_code, $url) : '';
            }
        }
    }

    /**
     * Method to build cURL and make request.
     * @return object cURL Handler Response and Info
     * @access private
     */
    private function _buildCurl($url)
    {
        $this->_curl = curl_init();
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        $this->_scResponse->data = json_decode(curl_exec($this->_curl));

        //fast and dirty way to convert array to object with json_encode/decode
        $this->_scResponse->info = json_decode(json_encode(curl_getinfo($this->_curl)));
        curl_close($this->_curl);
    }

    /**
     * build URL
     * @return string URL
     * @access private
     */
    private function _buildURL($destUrl)
    {
        $url = 'http://soundcloud.com/oembed?' . 'format=' . $this->_format;
        $url .= '&url=' . $destUrl;
        $url .= isset($this->maxwidth) ? '&maxwidth=' . $this->maxwidth : '';
        $url .= isset($this->maxheight) ? '&maxheight=' . $this->maxheight : '';
        $url .= isset($this->color) ? '&color=' . $this->color : '';
        $url .= isset($this->auto_play) ? '&auto_play=' . $this->auto_play : '';
        $url .= isset($this->show_comments) ? '&show_comments=' . $this->show_comments : '';
        $url .= isset($this->iframe) ? '&iframe=' . $this->iframe : '';
        return $url;
    }

    /**
     * cURL and Soundcloud API (HTTP) Error Code
     * @return string
     * @access private
     */
    private function _httpError($code, $url)
    {
        switch ($code) {
            case 400:
                $data = "Bad Request";
                break;
            case 401:
                $data = "Unauthorized";
                break;
            case 403:
                $data = "Forbidden";
                break;
            case 404:
                $data = "Not Found";
                break;
            case 406:
                $data = "Not Accessible";
                break;
            case 422:
                $data = "Unprocessable Entity";
                break;
            case 429:
                $data = "Too Many Requests";
                break;
            case 500:
                $data = "Internal Server Error";
                break;
            case 503:
                $data = "Service Unavailable";
                break;
            case 504:
                $data = "Gateway Timeout";
                break;
            default:
                $data = "Check cURL documentation for more information on this error code.";
                break;
        }
        return "<p>HTTP Error: " . $code . " - " . $data . "</p><p>url: " . $url;
    }

}

?>