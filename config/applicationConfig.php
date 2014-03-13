<?php
/**
 *  
 *  Copyright (C) 2014 paj@gaiterjones.com
 *
 *
 *  @category   PAJ tweetproduct
 * 	
 *
 */


	
/**
 * config class.
 -- application config
 */

// manual includes
include './php/Application.php';
include './php/Magento/Magento.php';
include './php/Magento/Collection/Collection.php';
include './php/Codebird/Codebird.php';
include './php/Twitter/Connect/Connect.php';
include './php/Twitter/Tweet/Tweet.php';
include './php/Bitly/Bitly.php';
include './php/Log/Log.php';

class config
{
// IMPORTANT!
// specify the full path to your configuration file here
	
const userConfigurationFile='/home/www/dev/magentoTweetProductDevConfig.ini';

// Edit configuration settings in INI file
//
//


	public function __construct()
	{
		$this->loadUserConfiguration();
	}
	
	protected function loadUserConfiguration()
	{
		
		$_userConfigFile=self::userConfigurationFile;
		
		if (file_exists($_userConfigFile))
		{
		   $_settings=parse_ini_file($_userConfigFile,'application_settings');
		} else {
		    die('The requested user configuration file - '. $_userConfigFile. ' does not exist. Please check your configuration file and settings.');
		}
		
		$_settings=$_settings['application_settings'];
		
		foreach ($_settings as $_setting => $_value)
		{
			if ($_value=='true')
			{
				$this->set($_setting,true);
			} else if ($_value=='false') {
				$this->set($_setting,false);
			} else {
				$this->set($_setting,$_value);
			}
		}

	}	
	
    public function get($variable) {
	
	    $constant = 'self::'. $variable;
	    
	    // get constant if defines
	    if(defined($constant)) {
	    
	        return constant($constant);
	    
	    } else {
	    
	    	// get array variable
	    	if(isset($this->__[$variable])) {
	        	return $this->__[$variable];
	        } else {
		        return false;
	        }
	        
	    }
	}
	
	public function set($key,$value)
	{
	    $this->__[$key] = $value;
	}	

	
}

?>