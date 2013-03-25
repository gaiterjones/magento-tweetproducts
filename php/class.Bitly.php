<?
/* 
*
* A PHP Class to create a bitly shortened URL
*
*
*/


class BitlyURL
{

	protected $__;
	
	public function __construct()
	{

		$this->set('bitlyURLSuccess',false);


	}
	
	public function getBitlyURL($url,$login,$appkey,$cachefolder,$format = 'xml',$history = 1)
	{
	
		$_cachedFile  = $cachefolder. md5($url);
		
		if(!file_exists($_cachedFile))
		{
		
			$_bitly = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url). '&format='.$format.'&history='.$history;
	
			$response = file_get_contents($_bitly);
	
			/* parse bitly resonse */
			$xml = simplexml_load_string($response);
			
			/* valid bitly response return short url*/
			if ($xml->status_txt == 'OK')
			{
				$_bitlyurl = $xml->data->url;
				$this->set('bitlyURLSuccess',true);
				
				// write bitly URL to cache file
				$fp = fopen($_cachedFile, 'w');
				fwrite($fp, $_bitlyurl);
				fclose($fp);
				
				return $_bitlyurl;
			} else {
			/* invalid bitly response return long url*/
				return $url;
			}
			
		} else {
		
			// read bitly URL from cache file
			$_bitlyurl = file($_cachedFile);
			$this->set('bitlyURLSuccess',true);
			
			// return first line of cache file
			return $_bitlyurl[0];
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
