<?php
/**
 *  Twitter API connect class - uses Codebird
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
 *
 */

class Application_Twitter_Connect
{
	protected $__config;
	protected $__cache;
	protected $__;
	public $__twitter;

	public function __construct($_variables) {
			
			$this->loadClassVariables($_variables);
			$this->loadConfig();
			$this->loadTwitterAPI();
			//$this->loadMemcache();
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

	/**
	 * memcache loader function.
	 * @what loads memcache class
	 * @access private
	 * @return nix
	 */		
	private function loadMemcache()
	{
		$this->__cache=new Application_Cache_Memcache();
	}		
	
		
	public function loadTwitterAPI()
	{
		$this->set('success',false);
		$this->set('output','Not defined');
		$this->set('errormessage','Not defined');
	
		// -- Load API credentials
		$_twitterAccessToken = $this->__config->get('twitterAccessToken');
		$_twitterAccessTokenSecret = $this->__config->get('twitterAccessTokenSecret');
		$_twitterConsumerKey = $this->__config->get('twitterConsumerKey');
		$_twitterConsumerSecret = $this->__config->get('twitterConsumerSecret');	
		$_twitterAccountName = $this->__config->get('twitterAccountName');

		try
		{	
			$this->__twitter = new Application_Codebird;
			
			$this->__twitter->setConsumerKey($_twitterConsumerKey, $_twitterConsumerSecret);
			$this->__twitter->setToken($_twitterAccessToken, $_twitterAccessTokenSecret);
			
		} catch (Exception $e) {
			// catch errors silently
			$this->set('errormessage',$e);
			throw new exception('Twitter error');
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
	

}

?>
