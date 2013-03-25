<?php
/**
 *
 *  Copyright (C) 2012 paj@gaiterjones.com
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
 
 	usage
 	Script to tweet Magento product info from Magento Product Collection
 	for Magento 1.4+
 	
 	silent - no ouput
 	flushcache - delete cache files
 	flushlogs - delete log files
 	notweet - suppress tweeting for debugging
 	showtrace - display error trace
	
 *
 *  @category   PAJ
 *  @package    
 *  @license    http://www.gnu.org/licenses/ GNU General Public License
 * 	
 */

/* Main application class */
class Application
{
	
	protected $__;
	protected $__config;
	
	public function __construct() {
		
			$this->set('errorMessage','');
			
		try
		{
			$this->loadConfig();
			$this->getProductCollection($this->get('collectiontype'));
			$this->getProducts(date("Y-m-d"));
			$this->tweetProducts();
			$this->communicateWithHuman();
			$this->writeLog($this->get('log'));
			exit;
		
		}
		catch (Exception $e)
	    {
	    	$_crlf="\n";
			$_errorMessage=$_crlf. 'An error has occurred : '. $e->getMessage(). $_crlf;
			$_showTrace=$this->get('showtrace');
			if ($_showTrace) {$_errorMessage=$_errorMessage. 'Trace:'. $e->getTraceAsString(). $_crlf; }
			$this->set('errorMessage',$_errorMessage);
			
			// --output error to x
	    	$this->communicateWithHuman();
	    	$this->writeLog($_errorMessage,'error');
	    	exit;
	    }
	}

		/**
		 * communicateWithHuman function.
		 * -- output to console
		 * @access private
		 * @return void
		 */
		private function communicateWithHuman()
		{
			
			$_silent=$this->get('silent');
			
			if (!$_silent)
			{
				echo $this->get('output').
				$this->get('errorMessage');
			}
	
		}

		/**
		 * loadConfig function.
		 * -- loads and sets app variables
		 * @access private
		 * @return void
		 */
		private function loadConfig()
		{
			
			$this->__config= new config();
			
			$this->set('output','');
			$this->set('log','');
			$this->set('silent',false);
			$this->set('notweet',false);			
			$this->set('showtrace',false);
			
			$this->set('version','v0.58 26.10.2012');
			
			$_fileCacheFolder=$this->__config->get('FILE_CACHE_FOLDER');
			$_mage=$this->__config->get('PATH_TO_MAGENTO_INSTALLATION'). 'app/Mage.php';
			
			if (!is_writable('./log')) {exit ('Error - The log folder is not writeable.');} 
			if (!is_writable($_fileCacheFolder)) {throw new Exception('The file cache folder '. $_fileCacheFolder. ' is not writeable.');} 
			if (!file_exists($_mage)) { throw new Exception('Magento Mage.php not found in '. $_mage. '. Please specify a valid Magento installation folder.');} 
			
			$this->set('collectiontype',$this->__config->get('collectionType'));
			$this->set('usedescriptiontext',$this->__config->get('useDecriptionText'));
			
			foreach($_SERVER['argv'] as $value)
			{
			  if ($value==="silent") { $this->set('silent',true);}
			  if ($value==="notweet") { $this->set('notweet',true);}
			  if ($value==="showtrace") { $this->set('showtrace',true);}
			  
			  if ($value==="flushcache")
			  {
					$_crlf="\n";
					$files = glob($this->__config->get('FILE_CACHE_FOLDER').'*'); // get all file names
					foreach($files as $file){ // iterate files
					  if(is_file($file))
					    unlink($file); // delete file
					}
					echo '//-->'. $_crlf. 'Cache files deleted.'. $_crlf .'<--//'. $_crlf;
					exit;
					
			  }
			  if ($value==="flushlogs")
			  {
					$_crlf="\n";
					$files = glob('./log/*.csv'); // get all file names
					foreach($files as $file){ // iterate files
					  if(is_file($file))
					    unlink($file); // delete file
					}
					echo '//-->'. $_crlf. 'Log files deleted.'. $_crlf .'<--//'. $_crlf;
					exit;
					
			  }
			}
	
		}	

		/**
		 * getProducts function.
		 * -- extracts products from collection
		 * @access private
		 * @param mixed $_currentDate
		 * @return void
		 */
		private function getProducts($_currentDate)
		{
			// -- function variables
			$_newProductCount=0;
			$_collection=$this->get('collection');
			$_collectionType=$this->get('collectiontype');
			$_useDecriptionText=$this->get('usedescriptiontext');
			
			// -- iterate magento product collection
			foreach ($_collection as $product) {
			
				// -- product variables
				$_sku = $product->getSku();
				$_name = $this->clean_up($product->getName());
				if ($_useDecriptionText==='long')
				{
					$_descriptionText=$product->getDescription();
				} else {
					$_descriptionText=$product->getShortDescription();
				}
				$_unitPrice = round($product->getPrice(),2);
				$_url=explode('?',$product->getProductUrl());
				$_url=$_url[0];
				$_id=$product->getId();
				$_imageURL=$_imageBaseURL. $product->getImage();
				$_productIsSaleable= $product->isSaleable();
						
				  
				$_newFromDate= substr($product->getData('news_from_date'),0,10);
				
				
				// -- parse product collection
				// -- get products with new from date selected and are SALEABLE
				//
				if ($_collectionType === 'newfromdate' && $_currentDate === $_newFromDate && $_productIsSaleable) {
	
				  		$_filteredProducts[$_id] = array 
						  (
						  "sku" => $_sku,
						  "name" => $_name,
						  "url" => $_url,
						  "description" => $_descriptionText
						  );
						  
				  	$_newProductCount++;
				}
	
			}
			
			// -- set class variables
			$this->set('filteredcollection',$_filteredProducts);
			$this->set('newproductcount',$_newProductCount);
			$this->set('currentdate',$_currentDate);
	
		}
	
		/**
		 * tweetProducts function.
		 * -- tweets products
		 * @access private
		 * @return void
		 */
		private function tweetProducts()
		{
			// -- function variables
			$_crlf="\n";
			$_notweet=$this->get('notweet');
			$_newProductCount=$this->get('newproductcount');
			$_collection=$this->get('filteredcollection');
			$_tweetCount=0;
			$_fileCacheFolder=$this->__config->get('FILE_CACHE_FOLDER');
			$_alreadyTweetedCount=0;
			$_currentDate=$this->get('currentdate');
			$_currentHour = date("H");
			$_tweetsPerSession=$this->__config->get('tweetsPerSession');
			$_tweetFromHour=$this->__config->get('tweetFromHour');
			$_tweetToHour=$this->__config->get('tweetToHour');
			
			// -- Load Twitter --
			$_twitter=new Twitter();
			
			// -- Load Bitly
			$_bitly = new BitlyURL();
			
			// output header
			$_output=$_output.$_crlf. '[Magento Product Tweet '. $this->get('version'). ']'. $_crlf. 'Session started - '. date("Y-m-d H:i:s").$_crlf.$_crlf.'[Search Results]'. $_crlf;

			foreach ($_collection as $id=>$product) {
			
			
				$_id=$id;
				$_sku = $product['sku'];
				$_name = $product['name'];
				$_url = $product['url'];
				$_url = $_bitly->getBitlyURL($_url,$this->__config->get('bitlyLogin'),$this->__config->get('bitlyAPI'),$_fileCacheFolder);
				
				$_description = $product['description'];
			
			  	$_tweetText=$this->prepareTweet($_name,$_url,$_description); // tweet text
			  	
			  	$_output=$_output.'//-->'. $_crlf.
			  						$_id.' - '. $_name.' / '. 
			  						$_sku. $_crlf. $_tweetText.
			  						' - '. strlen($_tweetText). $_crlf; // console output
			  						
			  	$_cachedFile = $_fileCacheFolder. $_currentDate. '-'. md5($_id.'_'.$_sku);
			  	
			  	// -- check if product already tweeted
			  	if(!file_exists($_cachedFile))
			  	{
			  		
			  		// -- validate time range
			  		if ($_currentHour >= $_tweetFromHour && $_currentHour <= $_tweetToHour) 
			  		{
				
						// -- control tweet limit
						if ($_tweetCount >= $_tweetsPerSession)
						{
							$_output=$_output. $_crlf. 'Session tweet limit (' . $_tweetsPerSession. ') reached, ending session.' .$_crlf;
							break;
						} else {
							
						  	// -- tweet
						  	if (!$_notweet)
						  	{
							  		$_twitter->tweet($_tweetText);
							  		$_success=$_twitter->get('success');
							  		$_accountverfiy=$_twitter->get('accountverify');
							  		if ($_success)
							  		{
								  		$_output=$_output. $_accountverfiy. ' - Tweet Success - '. date("Y-m-d H:i:s"). $_crlf;
							  		}	else {
								  		$_output=$_output. $_accountverfiy. ' - Tweet Fail - '. date("Y-m-d H:i:s"). $_crlf;
							  		}
						  	}							
						}
						
				  	// -- write product cache file
					$fp = fopen($_cachedFile, 'w');
					fwrite($fp, $_name);
					$_output=$_output.'Cache file '. $_cachedFile. ' created.'. $_crlf;
					fclose($fp);
					
					$_tweetCount++;
					
				  	} else {
					  	$_output=$_output. $_crlf. 'Skipping, outwith time range ('. $_currentHour. '00) - '. $_tweetFromHour. '00-'. $_tweetToHour. '00.'. $_crlf;
				  	}

			  	} else {
				  	$_output=$_output.'Cache file '. $_cachedFile. ' exists - ignoring product.'. $_crlf;
				  	$_alreadyTweetedCount++;
			  	}		
			
			$_output=$_output.'<--//'. $_crlf;
			}
			
						
			// -- unload Twitter
			unset ($_twitter);
			// -- unload bitly
			unset ($_bitly);
	
			// -- create console output
			$_output=$_output.
				 $_crlf.
				 '[Summary for collection type - '.$this->get('collectiontype'). ']'. $_crlf.
				 $_newProductCount. ' product/s found.'. $_crlf.
				 $_tweetCount. ' product/s tweeted this session.'. $_crlf.
				 ($_alreadyTweetedCount+$_tweetCount). ' product/s tweeted today - '. $_currentDate. '.'. $_crlf.
				 $_crlf;
				 
		    // -- create summary for log
		    $_summary='Summary for collection type - '.$this->get('collectiontype'). ', '.
		     $_newProductCount. ' product/s found, '. $_tweetCount. ' product/s tweeted this session, '.
				 ($_alreadyTweetedCount+$_tweetCount). ' product/s tweeted today - '. $_currentDate. '.';
				 
			// set class variables 
			$this->set('output',$_output);
			$this->set('log',$_summary);

			
			
		}

		/**
		 * getProductCollection function.
		 * -- gets Magento product collection
		 * @access private
		 * @param mixed $_collectionType
		 * @return void
		 */
		private function getProductCollection($_collectionType)
		{
			// -- Load Magento --
			$_obj=new MagentoCollection();
			
			switch ($_collectionType)
			{			
				case 'newfromdate':
				$_obj->getNewProducts();
				break;
				
				case 'allproducts':
				$_obj->getAllProducts();
				break;
				
				default:
				throw new Exception('Invalid collection type.');
			}
			
			// -- load Magento Product Collection
			$_collection=$_obj->get('collection');
			
			$_imageBaseURL=$_obj->get('baseurlmedia'). 'catalog/product';
	
			$this->set('imagebaseurl',$_imageBaseURL);
			$this->set('collection',$_collection);
			
			// -- Unload Magento
			unset($_collection);
			unset($_obj);
		}


		/**
		 * prepareTweet function.
		 * -- prepares text for tweeting
		 * @access private
		 * @param mixed $_name
		 * @param mixed $_url
		 * @param mixed $_text
		 * @return void
		 */
		private function prepareTweet($_name,$_url,$_text)
		{
			
					$tweetMainText='';
					
					// twitter text prefix
					$tweetHeadline=$this->__config->get('tweetHeadline'). ' ';
					$tweetMainText=$tweetMainText. $tweetHeadline. $_name. ', ';
										
					// get product URL
					$productURL=$_url;
					// Tweet suffix
					$tweetMainTextSuffix = '... ';
					// calculate Tweet length.
					$tweetCharsRemaining=140-(strlen($productURL)+strlen($tweetMainTextSuffix));
					// main description text long
					//$mainText = trim($this->clean_up(str_replace("<br />",", ",substr($_text, 0, strpos($_text, '.')+1)))). ' ';
					$mainText=$this->clean_up($_text);
					// main text for tweet
					$tweetMainText=$tweetMainText . $mainText;
					
					// only tweet 140 chars or less
					if (strlen($tweetMainText) <= $tweetCharsRemaining) {
						$tweetMainText = $tweetMainText; //do nothing
					} else {
						$tweetMainText = wordwrap($tweetMainText, $tweetCharsRemaining);
						$tweetMainText = substr($tweetMainText, 0, strpos($tweetMainText, "\n"));
						$tweetMainText = $tweetMainText. $tweetMainTextSuffix;
					}
		
					$tweetMainText = $tweetMainText. $productURL;
					return $tweetMainText;
					
		}
		
		
		/**
		 * clean_up function.
		 * -- clean up a string
		 * @access private
		 * @param mixed $_text
		 * @return void
		 */
		private function clean_up ($_text)
		{
			$_cleanText=$this->replaceHtmlBreaks($_text," ");
			$_cleanText=$this->strip_html_tags($_cleanText);
			$_cleanText=preg_replace("/&#?[a-z0-9]+;/i"," ",$_cleanText);
			//$_cleanText=htmlspecialchars($_cleanText);
			
			return $_cleanText;
		}
				
		private function strip_html_tags( $_text )
		{
		    $_text = preg_replace(
		        array(
		          // Remove invisible content
		            '@<head[^>]*?>.*?</head>@siu',
		            '@<style[^>]*?>.*?</style>@siu',
		            '@<script[^>]*?.*?</script>@siu',
		            '@<object[^>]*?.*?</object>@siu',
		            '@<embed[^>]*?.*?</embed>@siu',
		            '@<applet[^>]*?.*?</applet>@siu',
		            '@<noframes[^>]*?.*?</noframes>@siu',
		            '@<noscript[^>]*?.*?</noscript>@siu',
		            '@<noembed[^>]*?.*?</noembed>@siu',
		          // Add line breaks before and after blocks
		            '@</?((address)|(blockquote)|(center)|(del))@iu',
		            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
		            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
		            '@</?((table)|(th)|(td)|(caption))@iu',
		            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
		            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
		            '@</?((frameset)|(frame)|(iframe))@iu',
		        ),
		        array(
		            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
		            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
		            "\n\$0", "\n\$0",
		        ),
		        $_text );
		    return strip_tags( $_text );
		}
		
		private function replaceHtmlBreaks($str, $replace, $multiIstance = FALSE)
		{
		  
		    $base = '<[bB][rR][\s]*[/]*[\s]*>';
		    
		    $pattern = '|' . $base . '|';
		    
		    if ($multiIstance === TRUE) {
		        //The pipe (|) delimiter can be changed, if necessary.
		        
		        $pattern = '|([\s]*' . $base . '[\s]*)+|';
		    }
		    
		    return preg_replace($pattern, $replace, $str);
		}

		private function writeLog($_logText,$_level="info")
		{
			$_log = new MyLogPHP('./log/debug.log.'. date("Y-m-d"). '.csv');
			
			if ($_level==='info') {$_log->info($_logText);}
			if ($_level==='error') {$_log->error($_logText);}
			unset($log);
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
