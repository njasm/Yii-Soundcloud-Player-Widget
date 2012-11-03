<?php
/**
 * Yii Framework Widget to embed Soundcloud html5 Player into Yii Framework web apps.
 *
 * @author      Nelson J Morais <njmorais@gmail.com>
 * @copyright   2012 Nelson J Morais <njmorais@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://github.com/njasm/soundcloud
 * @category    Services
 * @package     Soundcloud
 * @version     0.0.1
 */

class yiiSoundcloudPlayerWidget extends CWidget {

    /**
     * cURL Handler
     * @var Object
     * @access private
     */
    private $_curl;
    
    /**
     * Data que chega da API em formato json
     * @var Object
     * @access private
     */
    private $_scResponse;
    
    /**
     * Control parameter to check if cURL extension is loaded
     * @var boolean
     * @access private
     */
    private $_curlLoaded;
    
    /**
     * SOUNDCLOUD DEFINITION: A Soundcloud URL for a track, set, group, user.
     * @var string
     * @access public
     */
    public $url;
    
    /**
     * SOUNDCLOUD DEFINITION: (optional) Either xml, json or js (for jsonp). 
     * Since we're only echoing the embed code recieved the developer doesn't need to access this parameter.
     * @var string
     * @access private
     */
    private $_format = '.json';
    
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
        
        
    public function init() {       
        // lets check if we have cURL extension
        $this->_curlLoaded = (in_array('curl', get_loaded_extensions(false))) ? true : false;
    }    
    
    public function run() {
        // if we don't have cURL extension stop everything :(
        if (!$this->_curlLoaded) {
            echo '<p>We need cURL extension loaded in php to be able to run. today is a sad day ;(</p>';
            return;
        }
        
        // any problem with $this->url ?!
        if (empty($this->url)) {
            echo '<p>No "url" parameter.</p>';
            return;
        }
      
        // is $this->url an array?!
        if (is_array($this->url)) {
            foreach($this->url as $url) {
                //call _buildCurl method with needed URL param
                $this->_buildCurl($this->_buildURL($url));
                echo $this->_scResponse->data->html;
            }
            return;
        }

        //call _buildCurl method with needed URL param
        $this->_buildCurl($this->_buildURL($this->url));
        echo $this->_scResponse->data->html;
    }
      
    /**
     * Method to build cURL and make request.
     * @return Array cURL Handler Response and Info
     * @access private
     */
    private function _buildCurl($url) {
        $this->_curl = curl_init();     
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        $this->_scResponse->data = json_decode(curl_exec($this->_curl)); 
        $this->_scResponse->info = curl_getinfo($this->_curl);
        curl_close($this->_curl);
    }
    
    /**
     * build URL
     * @return string URL
     * @access private
     */
    private function _buildURL($destUrl) {
        $url = 'http://soundcloud.com/oembed?' . 'format=json&';
        $url .= 'url=' . $destUrl;
        $url .= isset($this->maxwidth) ? '&maxwidth=' . $this->maxwidth : '' ;
        $url .= isset($this->maxheight) ? '&maxheight=' . $this->maxheight : '';
        $url .= isset($this->color) ? '&color=' . $this->color : '';
        $url .= isset($this->auto_play) ? '&auto_play=' . $this->auto_play : '';
        $url .= isset($this->show_comments) ? '&show_comments=' . $this->show_comments : '';
        $url .= isset($this->iframe) ? '&iframe=' . $this->iframe : '';
        return $url;
    }
}
?>