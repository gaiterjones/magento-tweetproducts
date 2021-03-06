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
 *
 *  @category   PAJ
 *  @package    
 *  @license    http://www.gnu.org/licenses/ GNU General Public License
 * 	
 *
 */

 
class Application_Magento_Collection extends Application_Magento {



	public function __construct() {

		parent::__construct();


	}
	

	public function getNewProducts()
	{  
 
			// load collection
			$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
			$storeId    = Mage::app()->getStore()->getId();
 
			$collection = Mage::getModel('catalog/product')
                    ->getCollection()   
                    ->setStoreId($storeId)
	                 ->addStoreFilter($storeId)  
					 ->addAttributeToFilter('status', 1)
					 ->addAttributeToFilter('visibility', 4)
					 ->addAttributeToFilter('is_saleable', TRUE)
					 ->addAttributeToSelect('sku')
					 ->addAttributeToSelect('name')
					 ->addAttributeToSelect('description')
					 ->addAttributeToSelect('short_description')
					 ->addAttributeToSelect('url')
					 ->addAttributeToSelect('image')
					 ->addAttributeToSelect('price')             
                     ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
                     ->addAttributeToFilter('news_to_date', array('or'=> array(
                        0 => array('date' => true, 'from' => $todayDate),
                        1 => array('is' => new Zend_Db_Expr('null')))
                     ), 'left')
                     ->addAttributeToSort('news_from_date', 'desc')
                     ->addAttributeToSort('created_at', 'desc');   

        
            $this->set('collection',$collection); 
	}


	public function getBestsellingProducts()
	
	//Get Bestselling products for last 30 days
	
	{

    // number of products to display
    $productCount = 5;
     
    // store ID
    $storeId    = Mage::app()->getStore()->getId();
 
    // get today and last 30 days time
    $today = time();
    $last = $today - (60*60*24*30);
 
    $from = date("Y-m-d", $last);
    $to = date("Y-m-d", $today);
     
    // get most viewed products for current category
    $products = Mage::getResourceModel('reports/product_collection')
                    ->addAttributeToSelect('*')     
                    ->addOrderedQty($from, $to)
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)                  
                    ->setOrder('ordered_qty', 'desc')
                    ->setPageSize($productCount);
     
    Mage::getSingleton('catalog/product_status')
            ->addVisibleFilterToCollection($products);
    Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInCatalogFilterToCollection($products);
     
     $this->set('collection',$products); 
	}

	public function getAllProducts()
	{  
 
		$storeId    = Mage::app()->getStore()->getId();  
		
		$collection = Mage::getModel('catalog/product')
                         ->getCollection()
                         ->setStoreId($storeId)
	                     ->addStoreFilter($storeId)  
						 ->addAttributeToFilter('status', 1)
						 ->addAttributeToFilter('visibility', 4)
						 ->addAttributeToSelect('sku')
						 ->addAttributeToSelect('name')
						 ->addAttributeToSelect('description')
						 ->addAttributeToSelect('short_description')
						 ->addAttributeToSelect('url')
						 ->addAttributeToSelect('image')
						 ->addAttributeToSelect('price')
                         ->addAttributeToSort('name', 'ASC');
		
		$this->set('collection',$collection); 

	}

	public function getOverallBestsellingProducts()
	{  
	// Get overall Bestselling products
	    // number of products to display
	    $productCount = 5;
	     
	    // store ID
	    $storeId    = Mage::app()->getStore()->getId();      
	     
	    // get most viewed products for current category
	    $products = Mage::getResourceModel('reports/product_collection')
	                    ->addAttributeToSelect('*')     
	                    ->addOrderedQty()
	                    ->setStoreId($storeId)
	                    ->addStoreFilter($storeId)                  
	                    ->setOrder('ordered_qty', 'desc')
	                    ->setPageSize($productCount);
	     
	    Mage::getSingleton('catalog/product_status')
	            ->addVisibleFilterToCollection($products);
	    Mage::getSingleton('catalog/product_visibility')
	            ->addVisibleInCatalogFilterToCollection($products);
	     
	    $this->set('collection',$collection); 
	}


}