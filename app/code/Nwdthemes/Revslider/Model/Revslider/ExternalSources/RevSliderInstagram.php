<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\ExternalSources;

/**
 * Instagram
 *
 * with help of the API this class delivers all kind of Images from instagram
 *
 * @package    socialstreams
 * @subpackage socialstreams/instagram
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderInstagram {

    protected $_framework;

	/**
	 * API key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    Instagram API key
	 */
	private $api_key;

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
   * Transient seconds
   *
   * @since    1.0.0
   * @access   private
   * @var      number    $transient Transient time in seconds
   */
  private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Instagram API key.
	 */
	public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $framework,
        $api_key,
        $transient_sec=1200
    ) {
        $this->_framework = $framework;

		$this->api_key = $api_key;
		$this->transient_sec = $transient_sec;
	}

    /**
     * Get Instagram Pictures Public by User
     *
     * @since    1.0.0
     * @param    string    $user_id   Instagram User id (not name)
     */
    public function get_public_photos($search_user_id,$count)
    {
        //call the API and decode the response
        if (!is_numeric($search_user_id)) $search_user_id = $this->get_user_id($search_user_id);

        if (!empty($search_user_id) && !empty($this->api_key)) {

            $url = 'https://api.instagram.com/v1/users/' . $search_user_id . '/media/recent/?&access_token=' . $this->api_key . '&count=' . $count;

            $transient_name = 'revslider_' . md5($url);

            $rsp = json_decode($this->_framework->wp_remote_fopen($url));

            if (isset($rsp->data)) {
                for ($i = 0; $i < $count; $i++) {
                    if (isset($rsp->data[$i])) {
                        $return[] = $rsp->data[$i];
                    }
                }
                $rsp->data = $return;
                $this->_framework->set_transient($transient_name, $rsp->data, $this->transient_sec);
                return $rsp->data;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * Get user ID if necessary
     * @since 5.4.6.3
     */
    public function get_user_id($search_user_id) {
        $url = 'https://api.instagram.com/v1/users/search?q='.$search_user_id.'&access_token='.$this->api_key;

        // check for transient
        $transient_name = 'essgrid_' . md5($url);
        if ($this->transient_sec > 0 && false !== ($data = $this->_framework->get_transient( $transient_name)))
            return ($data);

        // contact API
        $rsp = json_decode($this->_framework->wp_remote_fopen($url));

        // set new transient
        if(isset($rsp->data[0]))
            $this->_framework->set_transient( $transient_name, $rsp->data[0]->id, 604800 );

        // return user id
        if(isset($rsp->data[0]))
            return $rsp->data[0]->id;
        else
            return false;
    }

    /**
     * Get Instagram Pictures Public by Tag
     *
     * @since    1.0.0
     * @param    string    $user_id     Instagram User id (not name)
     */
    public function get_tag_photos($search_tag,$count){
        //call the API and decode the response
        $url = "https://www.instagram.com/explore/tags/".$search_tag."/?__a=1";

        $transient_name = 'revslider_' . md5($url);

        $rsp = json_decode($this->_framework->wp_remote_fopen($url));

        for($i=0;$i<$count;$i++) {
            $return[] = $rsp->tag->media->nodes[$i];
        }

        if(isset($rsp->tag->media->nodes)){
            $rsp->tag->media->nodes = $return;
            $this->_framework->set_transient( $transient_name, $rsp->tag->media->nodes, $this->transient_sec );
            return $rsp->tag->media->nodes;
        }
        else return '';
    }
}