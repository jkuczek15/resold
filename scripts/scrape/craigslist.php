<?php
/**
 * craigslist.php
 *
 * This script is used to retreive craigslist data from different sections
 * of the website. Most options are configurable under the config section.
 *
 * The output of the script will then be used to send emails via gmail in the user's web browser.
 * This is done to avoid spam detection and can be found in the puppeteer-gmail folder.
 *
 * Run "composer install" and "npm install" from the root directory to install all dependencies.
 * Puppeteer is required to run Javascript and scrape emails correctly. "npm install" should also
 * be run from the puppeteer-gmail directory
 *
 * Version Rev. 1.0.0
 */
######################################
######################################
############# ENVIRONMENT ############
######################################
######################################
set_time_limit(0);

######################################
######################################
############# INCLUDES ###############
######################################
######################################
include('includes/simple_html_dom.php');
include('includes/console.php');
include('vendor/autoload.php');

######################################
######################################
############# NAMESPACES #############
######################################
######################################
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

######################################
######################################
############# CONFIG #################
######################################
######################################
// location configuration
$locations = [
  'Chicago' => [
    'url' => 'https://chicago.craigslist.org',
    'latitude' => '41.8781',
    'longitude' => '-87.6298'
  ],
  'New York' => [
    'url' => 'https://newyork.craigslist.org',
    'latitude' => '40.7128',
    'longitude' => '-74.0060'
  ],
  'San Francisco' => [
    'url' => 'https://sfbay.craigslist.org',
    'latitude' => '37.7749',
    'longitude' => '-122.4194'
  ],
  'Los Angeles' => [
    'url' => 'https://losangeles.craigslist.org',
    'latitude' => '34.0522',
    'longitude' => '-118.2437'
  ],
  'Houston' => [
    'url' => 'https://houston.craigslist.org',
    'latitude' => '29.7604',
    'longitude' => '-95.3698'
  ],
  'Phoenix' => [
    'url' => 'https://phoenix.craigslist.org',
    'latitude' => '33.4484',
    'longitude' => '-112.0740'
  ],
  'San Diego' => [
    'url' => 'https://sandiego.craigslist.org',
    'latitude' => '32.7157',
    'longitude' => '-117.1611'
  ]
];

// mapping between craigslist search url and Resold category
$url_parts = [
  '/search/sss?sort=pricedsc&max_price=1200&min_price=50&query=collectibles' => [224]
];

// URL crawling ignores
$posts_regex_ignores = [
  '/#/',
  '/search/',
  '/https:\/\/www.craigslist.org\/about\//',
  '/play.google/',
  '/apps.apple/',
  '/accounts./',
  '/forums./',
  '/post./',
  '/\/\/www.craigslist.org\/about/'
];
$posts_string_ignores = ['/', ''];

// limits
$max_page_count = 1;             // number of pages deep we should crawl per URL part
$max_page_post_count = 5;       // number of posts we should scrape per page
$max_images = 2;               // number of images to download per post
$reply_sleep_time = 4;        // time to wait before copying email from reply button

######################################
######################################
############# PUPPETEER ##############
######################################
######################################
$puppeteer = new Puppeteer;
$timeout = 0;

// launch a new instance of puppeteer chromium browser
$browser = $puppeteer->launch(['headless' => false]);

######################################
######################################
############# CSV SETUP ##############
######################################
######################################
$output_file_path = 'email-sender/db/posts-list.txt';
$fp = fopen($output_file_path, "w");

######################################
######################################
############# IMAGE FOLDER ###########
######################################
######################################
$base_image_path = '/var/www/html/pub/media/catalog/craigslist/';
if(file_exists($base_image_path)){
  deleteDir($base_image_path);
}// end if folder exists

mkdir($base_image_path);
chmod($base_image_path, 0777);

######################################
######################################
############# CRAWL ##################
######################################
######################################
// keep track of what we already crawled
$scraped_urls = [];
$total_post_count = 1;

// loop over different URL parts (categories/searches)
foreach($url_parts as $url_part => $category_ids)
{
  // loop over locations
  foreach($locations as $location_city => $location_config)
  {
    $base_url = $location_config['url'];
    $posts_url = $base_url.$url_part;
    $page_count = 1;
    echo Console::light_blue('Beginning scrape on post part: '.$posts_url.' ('.$location_city.')') . "\r\n";
    do
    {
      // loop while we have pages to loop over
      try
      {
        // scrape the initial posts links
        echo Console::green('- Scraping post page '.$page_count.': '. $posts_url) . "\r\n";
        $posts_html = file_get_html($posts_url);
        $post_links = filterLinks($posts_html->find('a'), $posts_regex_ignores, $posts_string_ignores);
        $page_post_count = 1;
        foreach($post_links as $key => $user_post_link)
        {
          try
          {
            echo Console::cyan('-- Scraping post: '. $user_post_link) . "\r\n";
            $page = $browser->pages()[0];
            $page->goto($user_post_link, ['waitUntil' => 'load', 'timeout' => $timeout]);

            // Click the reply button and wait for the content to load
            $thumbs_selector = '#thumbs > a:nth-child(2)';
            if($page->querySelector($thumbs_selector)){
              $page->hover($thumbs_selector);
            }// end if we have multiple images

            $page->evaluate(JsFunction::createWithBody("$('.reply-button ').click()"));

            // wait for email content to load
            sleep($reply_sleep_time);

            // evaluate the email and return the result
            $result = $page->evaluate(JsFunction::createWithBody("

            $('.print-information.print-qrcode-container').remove();
            let images = [$('.swipe-wrap > div > img').attr('src')];

            $('.swipe-wrap > div > picture > img').each(function(index, element) {
              images.push($(element).attr('src'));
            });

            return {
              email: $('.mailapp').html(),
              title: $('#titletextonly').html(),
              description: $('#postingbody').html(),
              condition: $('.attrgroup > span > b').html(),
              price: $('.price').html(),
              location: $('.postingtitletext > small').html(),
              timeago: $('.timeago').html(),
              url: window.location.href,
              images: images
            }"));

            // check if we were able to scrape an email by evaluating javascript
            if(isset($result['email']) && $result['email'] !== null)
            {
              $result['category_ids'] = $category_ids;
              $result['post_count'] = $total_post_count;
              $result['latitude'] = $location_config['latitude'];
              $result['longitude'] = $location_config['longitude'];
              $result['location_city'] = $location_city;

              // setup image folder
              $image_folder = $base_image_path.'post-'.$total_post_count++.'/';
              mkdir($image_folder);
              chmod($image_folder, 0777);

              // download images
              $images = $result['images'];
              foreach($images as $key => $image_url)
              {
                $image_path = $image_folder.$key.'.jpg';
                file_put_contents($image_path, file_get_contents_curl($image_url));
                if($key == $max_images-1) {
                  break;
                }// end if max images reached
              }// end foreach loop over images

              fputcsv($fp, formatResult($result));
            }// end if email is set

            if($page_post_count++ == $max_page_post_count)
            {
              break;
            }// end if we've reached the max post count for this page
          }
          catch (Exception $e)
          {
            echo Console::red('Error visiting page: '. $e->getMessage()) . "\r\n";
            echo $user_post_link . "\r\n";
            $page->close();
          }// end try-catch visiting a page

        }// end foreach loop over post links

        // keep track of the pages we've already scraped
        $scraped_urls[] = $posts_url;

        // fetch the next page of posts
        $next_link_arr = filterLinks($posts_html->find('a'), [], [], '/s=/');
        $next_link_arr = array_filter($next_link_arr, "searchCheck");
        rsort($next_link_arr);
        $posts_url = isset($next_link_arr[0]) ? $base_url.$next_link_arr[0] : null;
      }
      catch (Exception $e)
      {
        echo Console::red('Error scraping post: '.  $e->getMessage()) . "\r\n";
        $browser->close();
        $browser = $puppeteer->launch(['headless' => $headless]);
      }// end try-catch

    } while($posts_url != null && !in_array($posts_url, $scraped_urls) && ++$page_count <= $max_page_count);

  }// end foreach loop over locations

}// end foreach loop over post parts

######################################
######################################
########## CLEANUP ###################
######################################
######################################
$browser->close();
fclose($fp);

######################################
######################################
############# FUNCTIONS ##############
######################################
######################################
/*
* filter an array of link elements according to $regex_ignores
* params: $links - array of of link elements
*         $regex_ignores - array of regular expressions to filter
*         $string_ignores - array of strings to filter
*         $single_match - single regular expression used to filter and return a match
*
* returns: $return_links - filtered string array of links
*/
function filterLinks($links, $regex_ignores = [], $string_ignores = [], $single_match = null)
{
  $return_links = [];
  foreach($links as $element)
  {
    $link = $element->href;
    if($single_match !== null)
    {
      $ignore = true;
      if(preg_match($single_match, $link))
      {
        $ignore = false;
      }// end if we found a single match
    }
    else
    {
      $ignore = false;
      foreach($regex_ignores as $regex_ignore)
      {
         if(preg_match($regex_ignore, $link) || in_array($link, $string_ignores))
         {
           $ignore = true;
         }// end if we found an ignore match
      }// end foreach loop over ignores

    }// end if we have a single match param

    if(!$ignore){
      $return_links[] = trim($link);
    }// end if not ignoring this link

  }// end foreach collecting links

  return array_unique($return_links);
}// end function filterLinks

/*
* function used to format result with email and query string
* params: $result       - result object from Puppeteer
*
* returns: $formattedResult
*/
function formatResult($result)
{
  foreach($result as $key => $value)
  {
    if(!is_array($value))
    {
      $result[$key] = trim($value);
    }
  }

  $email = $result['email'];
  $title = $result['title'];
  unset($result['email'], $result['timeago'], $result['url'], $result['images']);

  return [
    'email' => $email,
    'title' => $title,
    'queryString' => '?ap=1&'.http_build_query($result)
  ];
}// end function searchCheck

/*
* function used to safely retreive a value from an array

* returns: $formattedResult
*/
function getValue($key, $arr)
{
  if(isset($arr[$key])) {
    return $arr[$key];
  }
  return null;
}// end function getValue

/*
* function to recursively delete a directory

* returns: $data
*/
function deleteDir($dirPath)
{
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}// end function deleteDir

/*
* function to retreive data from a url

* returns: $data
*/
function file_get_contents_curl($url)
{
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);

  $data = curl_exec($ch);
  curl_close($ch);

  return $data;
}// end function file_get_contents_curl

/*
* function used to filter an array for 'next' page link
* params: $element - url for the next page request
*
* returns: $result - true/false based on regex match
*/
function searchCheck($element)
{
  global $base_url;
  return !preg_match("/".str_replace('/', '\/', $base_url)."/", $element);
}// end function searchCheck
