<?php
/*


*/

// manual includes
include './php/class.Application.php';
include './php/class.Magento.php';
include './php/class.MagentoCollection.php';
include './php/class.Twitter.php';
include './php/class.Bitly.php';
include './php/class.Log.php';


	
class config
{

// Edit configuration settings here
//
//
	// IMPORTANT!
	// path to root magento installation folder
	//
	const PATH_TO_MAGENTO_INSTALLATION = '/home/www/dev/magento/';
	
	// IMPORTANT!
	// path to file cacche folder, must be writeable by script
	//
	const FILE_CACHE_FOLDER = '/root/Dropbox/paj/www/dev/magento/tweetproducts/cache/';
	
	// IMPORTANT!
	// twitter api authentication details
	//
	const twitterAccessToken = 	'231812876-VpS2u0nIBPQrbcmOuocmNqMaT6anRo48niK971ol';
	const twitterAccessTokenSecret = 'TRmT5cvYr60ybCn58BkQ3KuoTeCfZVE8dNRVsyOffc';
	const twitterConsumerKey = 'uD023YaNEMatnfDsm0tsw';
	const twitterConsumerSecret = 	'h4xCJkC9SBhJMMoNIThwwLEXcc4Z6GhKUAgOqMau4o';
	const twitterAccountName = 	'gaiterjones';
	
	// bitly short url api authentication details
	//
	const bitlyAPI = 'R_5bb2fcaffa4c3bdc35df3167eaf7b788';
	const bitlyLogin = 'gaiterjones';
	
	// number of products to tweet per session
	//
	const tweetsPerSession = 	1;
	
	// headline for tweet
	//
	const tweetHeadline = 	'NEW TODAY!';
	
	// time range must be 00 through 23
	// only tweet products within this time range
	//
	const tweetFromHour = '09';
	const tweetToHour = '23';
	
	// text for product description
	// short - for short descriptiont text
	// long - for long description text
	// default is short.
	//
	const useDecriptionText = 'short';

	// collection type DO NOT CHANGE
	//
	const collectionType = 	'newfromdate';

	public function __construct()
	{

	}
	
    public function get($constant) {
	
	    $constant = 'self::'. $constant;
	
	    if(defined($constant)) {
	        return constant($constant);
	    }
	    else {
	        return false;
	    }
	}

	
}

//function autoloader($class) {
//	require_once 'php/class.' . $class . '.php';
//}

//spl_autoload_register('autoloader');
?>