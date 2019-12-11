<?php
######################################
######################################
########## INCLUDES ##################
######################################
######################################
include('/var/www/html/scripts/scrape/includes/simple_html_dom.php');

######################################
######################################
########## 3rd PARTY #################
######################################
######################################
include('/var/www/html/scripts/scrape/vendor/autoload.php');
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

######################################
######################################
########## CONFIG ####################
######################################
######################################
// output config
$output_file_path = 'csv/emails.csv';

// URL configuration
$base_url = 'https://chicago.craigslist.org';
$posts_part = '/search/ela';
$posts_url = $base_url.$posts_part;

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
$posts_string_ignores = ['/'];

// limits
$page_count = 2;
$reply_sleep_time = 5;

######################################
######################################
########## PUPPETEER #################
######################################
######################################
$puppeteer = new Puppeteer;
$browser = $puppeteer->launch(['headless' => true]);

######################################
######################################
########## COLLECT EMAILS ############
######################################
######################################
// loop over the post links collecting emails
$emails = [];
$count = 0;
do {
  // loop while we have pages to loop over
  $posts_html = file_get_html($posts_url);
  $post_links = filterLinks($posts_html->find('a'), $posts_regex_ignores, $posts_string_ignores);
  echo $posts_url . "\r\n";

  foreach($post_links as $user_post_link)
  {
    $page = $browser->newPage();
    $page->goto($user_post_link);

    // Click the reply button and wait for the content to load
    $result = $page->evaluate(JsFunction::createWithBody("$('.reply-button ').click()"));

    // wait for email content to load
    sleep($reply_sleep_time);

    // evaluate the email and return the result
    $result = $page->evaluate(JsFunction::createWithBody("
      return {
        email: $('.mailapp').html()
      }"));

    $page->close();

    if(isset($result['email']) && $result['email'] !== null)
    {
      $emails[] = $result['email'];
    }// end if email is set

  }// end foreach loop over post links

  // fetch the next link of posts
  $next_link_arr = filterLinks($posts_html->find('a'), [], [], '/s=/');
  $next_link_arr = array_filter($next_link_arr, "searchCheck");
  rsort($next_link_arr);
  $posts_url = isset($next_link_arr[0]) ? $base_url.$next_link_arr[0] : null;

} while($posts_url != null && ++$count <= $page_count);

######################################
######################################
########## PROCESS EMAILS ############
######################################
######################################
// write to a csv file for now
$emails = array_unique($emails);
$fp = fopen($output_file_path, "w");
foreach($emails as $email){
    fputcsv($fp, [$email], "\t");
}// end foreach over emails
fclose($fp);

######################################
######################################
########## FUNCTIONS #################
######################################
######################################
// filter an array of link elements according to ignores
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

// make a CURL request to a URL
function makeRequest($url)
{
  // CURL setup
  $curl = curl_init();
  curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $url
  ]);

  // Execute the request
  $result = curl_exec($curl);

  // Close request to clear up some resources
  curl_close($curl);

  return $result;
}// end function makeRequest

// array filter function
function searchCheck($element)
{
  return !preg_match("/https:\/\/chicago.craigslist.org/", $element);
}// end function searchCheck
