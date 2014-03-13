<?php
/**
 *  Tweet Class - uses Codebird
 '
 *  Copyright (C) 2013
 *
 *
 *  @who	   	PAJ
 *  @info   	paj@gaiterjones.com
 *  @license    blog.gaiterjones.com
 * 	
 *
  * 
 * http://tweet.ing-now.com/?ajax=true&class=AjaxTweet
 *
 */

class Application_Twitter_Tweet
{
	protected $__config;
	protected $__;

	public function __construct($_variables) {
			
			$this->loadClassVariables($_variables);
			$this->loadConfig();
			$this->tweet();
	}

	/**
	 * config loader function.
	 * @what loads app config
	 * @access private
	 * @return nix
	 */	
	private function loadConfig()
	{
		$this->__config= new config();
	}


	public function tweet()
	{
		$this->set('success',false);
		$this->set('output','Not defined');
		$this->set('errormessage','Not defined');
		
		$_tweetMainText=$this->get('tweetmaintext');
		$_twitterUserName=$this->get('twitterusername');
		
		$_dmMessage=false;
		$_mediaMessage=$this->get('tweetmedia');
		$_mediaFileName=$this->get('tweetmedia');
		
		// -- Load API credentials
		$_twitterAccessToken = $this->__config->get('twitterAccessToken');
		$_twitterAccessTokenSecret = $this->__config->get('twitterAccessTokenSecret');
		$_twitterConsumerKey = $this->__config->get('twitterConsumerKey');
		$_twitterConsumerSecret = $this->__config->get('twitterConsumerSecret');	
		$_twitterAccountName = $this->__config->get('twitterAccountName');
		
							

		try
		{	
			$cb = new Application_Codebird;
			
			$cb->setConsumerKey($_twitterConsumerKey, $_twitterConsumerSecret);
			$cb->setToken($_twitterAccessToken, $_twitterAccessTokenSecret);
			
			// Send an API request to verify credentials
			$_credentials = $cb->account_verifyCredentials();

			$this->set('accountverify','Connected as @'. $_credentials->screen_name);
			
			// verify twitter authentication
			if (strtolower($_credentials->screen_name)===strtolower($_twitterAccountName))
			{
				// tweet
				if (!$_dmMessage AND !$_mediaMessage)
				{
					// normal tweet with status update
					$cb->statuses_update(array('status' => $_tweetMainText));
				} else if ($_dmMessage AND !$_mediaMessage) {
					// tweet with direct message
					$cb->directMessages_new(array('text' => $_tweetMainText, 'screen_name' => $_twitterUserName));
				} else if ($_mediaMessage) {
					// tweet with media
					$cb->statuses_updateWithMedia(array('status' => $_tweetMainText, 'media[]' => $_mediaFileName));
				}
				

				$this->set('success',true);
				$this->set('output','Tweet sent '.($_dmMessage ? ' as DM Message to '. $_twitterUserName. '.' : ''). ' - '. $this->get('accountverify'));
				
			} else {
				$this->set('output','Tweet NOT sent');
			}
			
			unset ($cb);
			
		} catch (Exception $e) {
			// catch errors silently
			$this->set('errormessage',$e);
			throw new exception('Twitter error');
		}			
	}

	public function validateFollower($_username)
	{
		// -- Load API credentials
		$_twitterAccessToken = $this->__config->get('twitterAccessToken');
		$_twitterAccessTokenSecret = $this->__config->get('twitterAccessTokenSecret');
		$_twitterConsumerKey = $this->__config->get('twitterConsumerKey');
		$_twitterConsumerSecret = $this->__config->get('twitterConsumerSecret');	
		$_twitterAccountName = $this->__config->get('twitterAccountName');

		try
		{	
			$cb = new Application_Codebird;
			
			$cb->setConsumerKey($_twitterConsumerKey, $_twitterConsumerSecret);
			$cb->setToken($_twitterAccessToken, $_twitterAccessTokenSecret);
			
			// Send an API request to verify credentials
			$_credentials = $cb->account_verifyCredentials();

			$this->set('accountverify','Connected as @'. $_credentials->screen_name);
			
			// verify twitter authentication
			if (strtolower($_credentials->screen_name)===strtolower($_twitterAccountName))
			{
				
				$_followersData=$cb->followers_list(array('screen_name' => 'AjaxxTweet'));
				unset ($cb);
			
				foreach ($_followersData->users as $_follower)
				{
					$_followers[]=$_follower->screen_name;
				}
		
				if (in_array($_username, $_followers)) { return true; }
			
			} 
			
		} catch (Exception $e) {
			// catch errors silently
			return false;
		}

			return false;
	}
	
	public function validateMessage($_message)
	{
		if ($_message==="undefined") { return false; }
		return true;
	}

	public function set($key,$value)
	{
		$this->__[$key] = $value;
	}

	public function get($variable)
	{
		return $this->__[$variable];
	}
	
	private function loadClassVariables($_variables)
	{
		foreach ($_variables as $_variableName=>$_variableData)
		{
			// check for optional data
			if (substr($_variableName, -8) === 'optional') { continue; }
			
			$_variableData=trim($_variableData);
			if (empty($_variableData) && $_variableData !='0') {
				throw new exception('Class variable '.$_variableName. '('. $_variableData. ') cannot be empty.');
			}
			
			$this->set($_variableName,$_variableData);
						
		}
	}

	/**
	 * incLogCounter function.
	 * @what increments a counter in memcache
	 * @access public
	 * @return INTEGER COUNTER
	 */		
	private function incLogCounter($_cacheNameSpace)
	{
		$this->__cache->increment($_cacheNameSpace);
		return ($this->getLogCounter($_cacheNameSpace));
	}
	
	/**
	 * getLogCounter function.
	 * @what gets a memcache counter used to numerate logs
	 * @access protected
	 * @return INTEGER COUNTER
	 */	
	protected function getLogCounter($_cacheNameSpace)
	{
	
		$_counter = $this->__cache->cacheGet($_cacheNameSpace); // get version from cache
        
        if ($_counter === false) { // if namespace not in cache reset to 1
            $_counter = 1;
            $this->__cache->cacheSet($_cacheNameSpace, $_counter,7200); // save to cache note ttl in seconds
        }
        
        return $_counter;
        
	}		

}

?>
