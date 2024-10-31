<?php
/*
Plugin Name: My Trending Post Lite
Plugin URI: http://www.tranchesdunet.com/my-trending-post
Description: Allow you to search for trending topics on Twitter, and add a tweet with a content-related link to your blog
Author: Jean-Marc BIANCA (tranchesdunet)
Version: 0.2
Author URI: http://www.tranchesdunet.com/
TODO:
- si duplicate status, et un autre tweet existe, utiliser l'autre
*/

require_once(WP_PLUGIN_DIR.'/my-trending-post/includes/TwitterOAuth.class.php');

define("MYTRENDINGPOSTADMINURL", get_bloginfo('url')."/wp-admin/options-general.php?page=my-trending-post-lite.php");

define("MTP_CONSUMER_KEY","ht3VoaDXbFK0WfBC83doCg");
define("MTP_CONSUMER_SECRET","FPiLqM2EhAowSOQTMCs1iVB4BGfzRX9M5JhO92uorg");
define("MTP_OAUTH_CALLBACK",MYTRENDINGPOSTADMINURL."&action=callback");
if (!class_exists("myTrendingPost"))
{
  class myTrendingPost
  {
    var $adminOptionName = 'mytrendingpostAdminOptions';
    var $connection;
    
    function myTrendingPost()
    {
      $options = $this->getAdminOptions();
      global $connection;
      if(!empty($options['access_token']) && !empty($options['access_token']['oauth_token']) && !empty($options['access_token']['oauth_token_secret']))
      {
        $connection = new TwitterOAuth(MTP_CONSUMER_KEY, MTP_CONSUMER_SECRET, $options['access_token']['oauth_token'], $options['access_token']['oauth_token_secret']);
      }
      
      add_action('cron_mtp_sendtweet', array( &$this, 'setTweet' ));
      //$this->mtp_cron();
    }
    
    function printAdminPage()
    {
      $options = $this->getAdminOptions();
      $url_home = MYTRENDINGPOSTADMINURL;
      $url_clearsession = MYTRENDINGPOSTADMINURL."&action=clearsession";
      $url_connect = MYTRENDINGPOSTADMINURL."&action=connect";
      $url_callback = MYTRENDINGPOSTADMINURL."&action=callback";
      $url_redirect = MYTRENDINGPOSTADMINURL."&action=redirect";
      $delay = $options['delay'];
      $headdisplay = "";
      $bConnected = false;
      
      if(isset($_GET['action']))
      {
        switch($_GET['action'])
        {
          case 'clearsession': 
            $options['access_token'] = '';
            update_option($this->adminOptionName, $options);
            wp_redirect2($url_connect);
            break;
            
          case 'connect': 
            $headdisplay = "<a href='".$url_redirect."'><img src='".WP_PLUGIN_URL."/my-trending-post/images/sign_in_button.png' /></a>";
            break;
            
          case 'callback': 
            /* If the oauth_token is old redirect to the connect page. */
            if (isset($_REQUEST['oauth_token']) && $options['oauth_token'] !== $_REQUEST['oauth_token']) {
              $options['oauth_status'] = 'oldtoken';
              update_option($this->adminOptionName, $options);
              wp_redirect2($url_clearsession);
            }
            /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
            global $connection;
            $connection = new TwitterOAuth(MTP_CONSUMER_KEY, MTP_CONSUMER_SECRET, $options['oauth_token'], $options['oauth_token_secret']);
            
            /* Request access tokens from twitter */
            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
            
            /* Save the access tokens. Normally these would be saved in a database for future use. */
            $options['access_token'] = $access_token;
            
            /* Remove no longer needed request tokens */
            $options['oauth_token'] = '';
            $options['oauth_token_secret'] = '';
            update_option($this->adminOptionName, $options);
            /* If HTTP response is 200 continue otherwise send to connect page to retry */
            if (200 == $connection->http_code) {
              /* The user has been verified and the access tokens can be saved for future use */
              $options['status'] = 'verified';
              update_option($this->adminOptionName, $options);
              wp_redirect2($url_home);
            } else {
              /* Save HTTP status for error dialog on connnect page.*/
              wp_redirect2($url_clearsession);
            }
            break;
              
          case 'redirect': 
            /* Build TwitterOAuth object with client credentials. */
            $tempconnection = new TwitterOAuth(MTP_CONSUMER_KEY, MTP_CONSUMER_SECRET);
             
            /* Get temporary credentials. */
            $request_token = $tempconnection->getRequestToken(MTP_OAUTH_CALLBACK);
            
            /* Save temporary credentials to session. */
            $options['oauth_token'] = $token = $request_token['oauth_token'];
            $options['oauth_token_secret'] = $request_token['oauth_token_secret'];
            update_option($this->adminOptionName, $options);
//            echo "SESSION['oauth_token']: ".$_SESSION['oauth_token']."<br />";
            
            /* If last connection failed don't display authorization link. */
            switch ($tempconnection->http_code) {
              case 200:
                /* Build authorize URL and redirect user to Twitter. */
                $url = $tempconnection->getAuthorizeURL($token);
                wp_redirect2($url); 
                break;
              default:
                /* Show notification if something went wrong. */
                echo "<div class='error'>Could not connect to Twitter. Refresh the page or try again later.</div>";
            }
            break;
          case 'logout':
            $options['access_token'] = '';
            $options['oauth_token'] = '';
            $options['oauth_token_secret'] = '';
            update_option($this->adminOptionName, $options);
            wp_redirect2($url_connect);
            break;
          default: 
            //echo 'home<br />';
            break;
        }
      }else{
        if (empty($options['access_token']) || empty($options['access_token']['oauth_token']) || empty($options['access_token']['oauth_token_secret'])) {
          wp_redirect2($url_clearsession);
        }else{
          global $connection;
          $content = $connection->get('account/verify_credentials');
          if(!$content->error)
          {
            $headdisplay = "Logged with: ".$content->screen_name." <img src='".$content->profile_image_url."' /><br />";
            $headdisplay .= "<a href='".$_SERVER["REQUEST_URI"]."&action=logout'>Log Out</a><br />";
            $bConnected = true;
          }else{
            $headdisplay = $content->error."<br />";
            $headdisplay .= "<a href='".$_SERVER["REQUEST_URI"]."&action=logout'>Re-log</a><br />";
          }
        }
      }
      
      //sauvegarde des options
      if(isset($_POST['update_myTrendingPostSettings']))
      {
        if(isset($_POST['cats']))
        {
          $cats = implode(",",$_POST['cats']);
          $options['cats'] = $cats;
        }else{
          $options['cats'] = '';
        }
        
        update_option($this->adminOptionName, $options);
        print '<div class="updated"><p><strong>';
        _e("Settings updated","myTrendingPost");
        print '</strong></p></div>';
      }
      
      include('admin_settings.php');
    }//printadminpage
    
    //retrouve les trends et recherche dans la base un article correspondant
    function setTweet($bSendTweet = true)
    {
      $options = $this->getAdminOptions();
      global $connection;
      global $wpdb;
      $cats = $options['cats'];
      $topics = array();
      $current = $connection->get("trends/1");
      foreach ($current[0]->trends as $trend ) 
      {
        $topics[] = array('name' => $trend->name, 'url' => $trend->url);
      }
      
      //passer les hashtags en premier
      asort($topics);
      
      $totweet = array();
      
      echo "Current Trending Topics for the region World:<br />";
      echo "=============================================<br />";
      
      foreach($topics as $t)
      {
        echo "<h4><a href='".$t['url']."' target='_blank'>".$t['name']."</a>";//trending topic
        $t = str_replace("#","",$t['name']); //enlever les #
        $t = camelCaseToWords($t);
        if($t != $t['name'])
        {
          echo " ==> ".$t;
        }
        echo "</h4>";
        $datelimit = current_time('timestamp') - $options['delay'];//en timestamp
        $selectcat = "";
        $querycat = "";
        
        if(strlen($cats) > 0)
        {
          $selectcat = " LEFT JOIN ".$wpdb->term_relationships." rcat ON p.id = rcat.object_id
                LEFT JOIN ".$wpdb->term_taxonomy." tcat ON rcat.term_taxonomy_id = tcat.term_taxonomy_id 
                LEFT JOIN ".$wpdb->terms." tscat ON tcat.term_id = tscat.term_id ";
          $querycat = " AND tcat.taxonomy = 'category' AND tscat.term_id NOT IN (".$cats.") ";
        }
        
        $sql = "SELECT DISTINCT p.post_title, p.post_name, CONCAT('#','".$t."') AS hashtag, p.post_date AS post_date, p.id
                FROM ".$wpdb->posts." p
                LEFT JOIN ".$wpdb->postmeta." m ON p.ID = m.post_id AND m.meta_key = 'MTP_DATE'"
                .$selectcat."                
                WHERE p.post_status = 'publish' AND p.post_type = 'post' AND p.post_title REGEXP '[[:<:]]".$t."[[:>:]]'
                AND (m.meta_value IS NULL OR m.meta_value < ".$datelimit.")"
                .$querycat."
                UNION
                SELECT DISTINCT p.post_title, p.post_name, CONCAT('#','".$t."') AS hashtag, p.post_date AS post_date, p.id
                FROM ".$wpdb->posts." p
                INNER JOIN ".$wpdb->term_relationships." r ON p.id = r.object_id
                INNER JOIN ".$wpdb->term_taxonomy." t ON r.term_taxonomy_id = t.term_taxonomy_id
                INNER JOIN ".$wpdb->terms." ts ON t.term_id = ts.term_id AND ts.name = '".$t."'
                LEFT JOIN ".$wpdb->postmeta." m ON p.ID = m.post_id AND m.meta_key = 'MTP_DATE'"
                .$selectcat."
                WHERE p.post_status = 'publish' AND p.post_type = 'post'
                AND (m.meta_value IS NULL OR m.meta_value < ".$datelimit.")"
                .$querycat."
                ORDER BY RAND() DESC LIMIT 1";
        //print_r($sql);
        $fields = $wpdb->get_results($sql, ARRAY_A);
        //var_dump($fields);
        foreach($fields as $f)
        {
          $txt = $f['post_title'];
          $url = get_permalink($f['id']);
          $txt .= " ".$url." ";
          if(strlen($f['hashtag']) > 0)
          {
            $txt .= $f['hashtag'];
          }
          $totweet[] = array('txt' => $txt, 'topic' => $t, 'postid' => $f['id']);
          echo $txt."<br />";//texte du tweet
        }
      }
      
      //envoyer un tweet parmi tous ceux generés dans $totweet
      //TODO: si plusieurs tweet dispo, choisir en priorité ceux avec un hashtag
      //var_dump($totweet);
      $tweetchosen = null;
      if(count($totweet) > 0)
      {
        $tweetchosen = $totweet[0];
      }
      //var_dump($tweetchosen);
      //envoie du tweet
      if($tweetchosen)
      {
        $return = $this->sendTweet($tweetchosen, $bSendTweet);
        echo $return;
      }else{
        _e("ERROR: no matching post found!","myTrendingPost");
      }
      
    }
    
    //envoie le tweet, le sauvegarde dans les postmeta, et envoie un email de confirmation
    function sendTweet($tweet, $bSendTweet)
    {
      $options = $this->getAdminOptions();
      if(empty($options['access_token']) || empty($options['access_token']['oauth_token']) || empty($options['access_token']['oauth_token_secret']))
      {
        die;
      }
      //$connection = new TwitterOAuth(MTP_CONSUMER_KEY, MTP_CONSUMER_SECRET, $options['access_token']['oauth_token'], $options['access_token']['oauth_token_secret']);
      global $connection;
      $return = new stdClass();
      $returnstring = "<b>Tweet not sent:</b> ";
      if(true && $bSendTweet)
      {
        $return = $connection->post('statuses/update', array('status' => $tweet['txt']));
        $returnstring = "<b>Tweet sent:</b> ";
      }
      
      if($return->error)
      {
        $email_message = "ERROR: ".$return->error."\n\r Tweet: ".$tweet['txt'];
        return "ERROR: ".$return->error;
      }else{
        //logger le post et le tag envoyé pour ne pas envoyer 2 fois sur le meme dans un temps donné
        //on logge dans postmeta : un ligne avec la date en timestamp, et une ligne avec le tweet
        $postid = $tweet['postid'];
        $metadate = get_post_meta($postid, "mtp_date");
        $metatweet = get_post_meta($postid, "mtp_tweet");
        
        if(true) {
          if($metadate && $metatweet)//update des meta pour eviter de retweeter le meme article dans un delai donné
          {
            update_post_meta($postid, "mtp_date", time());
            update_post_meta($postid, "mtp_tweet", $tweet['txt']);
          }else{
            add_post_meta($postid, "mtp_date", time());
            add_post_meta($postid, "mtp_tweet", $tweet['txt']);
          }
        }
        return "<hr />".$returnstring.$tweet['txt'];
      }
    }
    
    //fonction appelée par le cron
    function mtp_cron()
    {
      if ( !wp_next_scheduled('cron_mtp_sendtweet') ){
  			wp_schedule_event(time(), 'hourly', 'cron_mtp_sendtweet');
  		}
  		//wp_clear_scheduled_hook('cron_mtp_sendtweet');
    }

    function getAdminOptions()
    {
      $mytrendingpostAdminOptions = array('oauth_token' => '',
                                          'oauth_token_secret' => '',
                                          'access_token' => '',
                                          'status' => '',
                                          'oauth_status' => '',
                                          'delay' => 604800,
                                          );
      //delay par defaut : 604800 = 7 jours
      $mytrendingpostOptions = get_option($this->adminOptionName);
      if(!empty($mytrendingpostOptions))
      {
        foreach($mytrendingpostOptions as $key => $option)
        {
          $mytrendingpostAdminOptions[$key] = $option;
        }
        update_option($this->adminOptionName, $mytrendingpostAdminOptions);
      }
      return $mytrendingpostAdminOptions;
    }
    
  }//class
}//if

if(class_exists("myTrendingPost"))
{
  $inst_myTrendingPost = new myTrendingPost();
}

if(!function_exists("myTrendingPost_ap"))
{
  function myTrendingPost_ap()
  {
    global $inst_myTrendingPost;
    if(function_exists("add_options_page"))
    {
      add_options_page("My Trending Post Lite", "My Trending Post Lite", 9, basename(__FILE__), array(&$inst_myTrendingPost, 'printAdminPage'));
    }
    wp_enqueue_style("my-trending-post", WP_PLUGIN_URL."/my-trending-post-lite/styles.css");
  }
}
add_action('admin_menu', 'myTrendingPost_ap');


if(!function_exists("wp_redirect2"))
{
  /**
   * 
   * workaround pour la fonction wp_redirect qui ne marche pas (headers already sent)
   * @param unknown_type $location
   */
  function wp_redirect2($location)
  {
    echo "<meta http-equiv='refresh' content='0;url=$location' />";
  }
}

if(!function_exists("camelCaseToWords"))
{
  /**
   * 
   * transforme les camelcase en phrase de mots separes
   * @param unknown_type $input
   */
  function camelCaseToWords($input)
  {
    $pass1 = preg_replace("/([a-z])([A-Z0-9])/","\\1 \\2",$input);//separe une minuscule suivie d'une majuscule ou d'un chiffre
    $pass2 = preg_replace("/([A-Z])([A-Z][a-z])/","\\1 \\2",$pass1);//separe une majuscule suivi d'une autre maj et d'une min.
    $pass3 = preg_replace("/([0-9])([A-Za-z])/","\\1 \\2",$pass2);//separe un chiffre suivi d'une majuscule ou minuscule
    return $pass3;
  }
}
//error_reporting(E_ERROR);
