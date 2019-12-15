<?php
set_time_limit(0);
/**
 * craigslist.php
 *
 * This script is used to retreive craigslist emails from different sections
 * of the website. Most options are configurable under the config section.
 *
 * Run "composer install" and "npm install" from the root directory to install all dependencies.
 * Puppeteer is required to run Javascript and scrape emails correctly.
 *
 * For educational purposes only
 * Enjoy :)
 *
 * Version Rev. 1.0.0
 */
######################################
######################################
############# INCLUDES ###############
######################################
######################################
include('includes/simple_html_dom.php');

######################################
######################################
############# 3RD PARTY ##############
######################################
######################################
include('vendor/autoload.php');
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

######################################
######################################
############# CONFIG #################
######################################
######################################
// URL configuration
$base_url = 'https://chicago.craigslist.org';
$posts_parts = [
  '/search/sya'
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
$page_count = 500;
$reply_sleep_time = 3;

######################################
######################################
############# PUPPETEER ##############
######################################
######################################
$puppeteer = new Puppeteer;
$headless = false;
$timeout = 0;

// launch a new instance of puppeteer chromium browser
$browser = $puppeteer->launch(['headless' => $headless]);

######################################
######################################
############# CSV SETUP ##############
######################################
######################################
$output_file_path = 'csv/emails.csv';
$fp = fopen($output_file_path, "w");

######################################
######################################
############# CRAWL ##################
######################################
######################################
// loop over the post links collecting emails
$scraped_urls = [];
foreach($posts_parts as $posts_part)
{
  $posts_url = $base_url.$posts_part;
  echo 'Beginning scrape on post part: '.$posts_url."\r\n";
  $count = 1;

  do {
    // loop while we have pages to loop over
    try {

      // scrape the initial posts links
      echo '- Scraping post page '.$count.': '. $posts_url . "\r\n";
      $posts_html = file_get_html($posts_url);
      $post_links = filterLinks($posts_html->find('a'), $posts_regex_ignores, $posts_string_ignores);

      foreach($post_links as $user_post_link)
      {
        echo '-- Scraping post: '. $user_post_link . "\r\n";
        try {
          $page = $browser->newPage();
          $page->goto($user_post_link, ['waitUntil' => 'load', 'timeout' => $timeout]);
        }catch (Exception $e){
          echo 'Error visiting page: ',  $e->getMessage(), "\r\n";
          echo $user_post_link . "\r\n";
          $page->close();
          continue;
        }// end try-catch visiting a page

        // Click the reply button and wait for the content to load
        $result = $page->evaluate(JsFunction::createWithBody("$('.reply-button ').click()"));

        // wait for email content to load
        sleep($reply_sleep_time);

        // evaluate the email and return the result
        $result = $page->evaluate(JsFunction::createWithBody("return { email: $('.mailapp').html() }"));

        // close the puppeteer page
        $page->close();

        // check if we were able to scrape an email by evaluating javascript
        if(isset($result['email']) && $result['email'] !== null)
        {
          $email = $result['email'];
          fputcsv($fp, [$email], "\t");
        }// end if email is set

      }// end foreach loop over post links

      // keep track of the pages we've already scraped
      $scraped_urls[] = $posts_url;

      // fetch the next link of posts
      $next_link_arr = filterLinks($posts_html->find('a'), [], [], '/s=/');
      $next_link_arr = array_filter($next_link_arr, "searchCheck");
      rsort($next_link_arr);
      $posts_url = isset($next_link_arr[0]) ? $base_url.$next_link_arr[0] : null;

    } catch (Exception $e){
      echo 'Error scraping post: ',  $e->getMessage(), "\r\n";
    }// end try-catch

  } while($posts_url != null && !in_array($posts_url, $scraped_urls) && ++$count <= $page_count);

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
* function used to filter an array for 'next' page link
* params: $element - url for the next page request
*
* returns: $result - true/false based on regex match
*/
function searchCheck($element)
{
  return !preg_match("/https:\/\/chicago.craigslist.org/", $element);
}// end function searchCheck
