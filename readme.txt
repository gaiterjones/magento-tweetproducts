Magento Product Tweet v0.5x

Introduction
Installation
Configuration
Usage

Introduction

Magento Product Tweet is an object oriented PHP script that integrates with a Magento installtion to automatically tweet product information for products set with the New From date attribute in the Magento database.

Magento Product Tweet works with Magento Community Edition version 1.4 or greater and required PHP 5.3 or greater.

Installation

Extract the source files to a folder on your system i.e. /home/php/magento

A new folder producttweet will be created.

Make sure the subfolders cache, and log are writeable by your current user.

Configuration

Edit the file applicationConfig.php in /producttweet/config.

Edit 
const PATH_TO_MAGENTO_INSTALLATION = '/home/www/dev/magento/';

With the actual path to your Magento installtion.

Edit
const FILE_CACHE_FOLDER = '/home/php/producttweet/cache/';

With the full path to the producttweet cache folder.

Edit the Twitter and Bitly api authentication details.

Edit the application configuration settings:

tweetsPerSession - this is the number of products that will be Tweeted each time the script runs.
tweetHeadline - this is the prefix to the Tweet text.
tweetFromHour - Time range to tweet between - from hour between 00 and 23
tweetToHour - Time range to tweet between - to hour between 00 and 23

e.g. to only tweet between 0900 and 2200 set the From hour to 09 and the To hour to 23.

useDecriptionText - set this to short or long to use either the short or long product description for the tweet text.




Save the file.

Usage

To run the script for the first time to check functionality change to the scripts working directory and type

php tweetproduct.php notweet

The script will execute normally but with tweeting disabled. If there are errors with your main configuration you will see them reported now. If no errors are reported the script will return a summary of all products in your Magento database with a New From date that matches todays date.

If no products are returned check that you have set the new from date attribute correctly on a product.

If products are found a summary of each products will be shown including an example of the Tweet text and URL e.g.

[Magento Product Tweet v0.56 22.10.2012]
Session started - 2012-10-22 18:12:33

[Search Results]
//-->
135 - Anashria Womens Premier Leather Sandal / ana
NEW TODAY! Anashria Womens Premier Leather Sandal, Buckle embellished contrasting straps adorn both the heel and... http://bit.ly/VrhMwA - 136
Cache file /root/Dropbox/paj/www/dev/magento/tweetproducts/cache/2012-10-22-9ac43d1842fd2477f500d028b158b496 created.
<--//
//-->
132 - SLR Camera Tripod / ac674
NEW TODAY! SLR Camera Tripod, Sturdy, lightweight tripods are designed to meet the needs of amateur and professional... http://bit.ly/VtXzX1 - 140

Session tweet limit (1) reached, ending session.

[Summary for collection type - newfromdate]
2 product/s found.
1 product/s tweeted this session.
1 product/s tweeted today - 2012-10-22.


If the results and Tweet examples are correct run the script again with the flushcache swith to delete the cache files :

php tweetproducts.php flushcache

And then run the script without the notweet switch to test live tweeting.

A tweet success or failure message will be displayed for each product, and the tweets should appear in your twitter feed.

Script activity will be logged in the ./log folder. You can delete the log files with the flushlogs switch.

To automate tweeting completely add the script to your crontab, e.g. to run the script every 5 minutes use

*/5 * * * * /usr/bin/php -f /home/php/magento/tweetproducts.php silent

Note the silent switch disables all script output.

If the script detects errors use the showtrace switch for more information and email the log file to extensions@gaiterjones.com for assistance.






PAJ 22.10.2012



