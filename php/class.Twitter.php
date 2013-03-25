<?php
/**
 *  A class to post a Twitter tweet using the oAuth method
 *  Twitter oAuth code by Abraham Williams - http://abrah.am/
 *
 *  Copyright (C) 2011 paj@gaiterjones.com
 *
 *	This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @category   PAJ
 *  @package    SocialMediaMarketing
 *  @license    http://www.gnu.org/licenses/ GNU General Public License
 * 
 *
 */

class Twitter
{
	protected $__config;
	protected $__;

	public function __construct() {
		
			$this->loadConfig();

	}

	// get app config
	private function loadConfig()
	{
		$this->__config= new config();
		$this->set('success',false);
	}
		
	public function tweet($tweetMainText)
	{

		// Read in our saved access token/secret
		// Get ready to tweet
		$twitterAccessToken = $this->__config->get('twitterAccessToken');
		$twitterAccessTokenSecret = $this->__config->get('twitterAccessTokenSecret');
		$twitterConsumerKey = $this->__config->get('twitterConsumerKey');
		$twitterConsumerSecret = $this->__config->get('twitterConsumerSecret');	
		$twitterAccountName = $this->__config->get('twitterAccountName');
		
		// Create twitter API object
		$ExternalLibPath='twitteroauth/twitteroauth.php';
		require_once ($ExternalLibPath);
		$oauth = new TwitterOAuth($twitterConsumerKey, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret);

		// Send an API request to verify credentials
		$credentials = $oauth->get("account/verify_credentials");
		//echo "Connected as @" . $credentials->screen_name;
		$this->set('accountverify','Connected as @'. $credentials->screen_name);
		
		// verify twitter authentication
		if (strtolower($credentials->screen_name)===strtolower($twitterAccountName))
		{
			// tweet
			$oauth->post('statuses/update', array('status' => $tweetMainText));
			$this->set('success',true);
			return;
		} else {
			$this->set('success',false);
			echo "Tweeet failed!". "\n";
		}

	}

	public function set($key,$value)
	{
		$this->__[$key] = $value;
	}

	public function get($variable)
	{
		return $this->__[$variable];
	}

}

?>
