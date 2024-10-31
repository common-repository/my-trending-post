<?php 
$cats = explode(",",$options['cats']);
?>
<div class=wrap>
  <div class="icon32" id="icon-edit">
    <br />
  </div>
  <h2>My Trending Post Lite</h2>
  
  <div class="metabox-holder has-right-sidebar">
      <div class="inner-sidebar">
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h3 class="hndle">
              <?php _e('Info & Support', 'hotlink2watermark') ?>
            </h3>
              <div class="inside">
                <?php _e("<b>My Trending Post Lite</b> is a plugin developped by <a href='http://www.tranchesdunet.com/'>Jean-Marc BIANCA</a>", "mytrendingpost") ?>
                <br />
                <ul>
                  <li><a href="http://www.tranchesdunet.com/my-trending-post"><?php _e("Support", "mytrendingpost")?></a></li>
                  <li><a href="https://twitter.com/#!/tranchesdunet">Follow me on Twitter</a></li>
                  <li><a href="http://www.facebook.com/tranchesdunet">Tranches du Net on Facebook</a></li>
                  <li><a href="http://wordpress.org/extend/plugins/my-trending-post/">Rate this plugin on Wordpress</a></li>
                </ul>
                <br />
                <a href="http://www.tranchesdunet.com/my-trending-post">Buy <b>My Trending Post</b> (Full Version) for 4,90€ only!</a><br />
            </div>
          </div>
        </div>
      </div>
      
      <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
      <div id="post-body-content">
        <div id="normal-sortables"
          class="meta-box-sortables ui-sortables">
          <div class="postbox">
            <h3 class="hndle">
            <?php _e('Settings', 'mytrendingpost') ?>
            </h3>
            <div class="inside">
              
            <?php 
              echo "<b>What you have with My Trending Post Lite:</b><br />
              <ul class='mptadmin'>
              	<li>An automatic tweet from a user defined twitter account, with a link related to one of your blog's post and the `World` trending topic (the plugin will check for Trending Topics every hours)</li>
              	<li>An <b>hashtag</b> will be added after the link, with the matched's topic</li>
              	<li>A <b>smart algorithm</b> to separate concatenated camel words<br />(e.g. #10FavouriteBands ==> 10 Favourite Bands) and so increase the matching between yours posts and the trending topics (often concatenated)</li>
              	<li>The post sent will be flagged and so won't be sent again until 7 days</li>
              	<li>The possibility of <b>excluding one or more categories</b> of article to be tweeted <span class='new'>(Now available in the Lite version!!)</span></li>
              </ul>
              <b>What you have in addition with My Trending Post (Full Version):</b><br />
              <ul class='mptadmin'>
              	<li>The <b>Zone choice</b> between `World` and any other countries covered by Twitter Trending Topics (e.g. USA, France, Japan, etc.)</li>
              	<li>The possibility of enabling/disabling the sending of the tweets</li>
              	<li>The possibility of enabling/disabling the sending of an email for each tweet sent (with email address and title settings)</li>
              	<li>The possibility of adding a text before the link in the tweet (like hashtag or everything else)</li>
              	<li>The possibility of adding a text after the link in the tweet (like hashtag or everything else)</li>
              	<li>The choice of a delay before retweeting the same article</li>
              	<li>The possibility to <b>use an url shortener</b> in your tweets (tinyurl or Bit.ly) to prevent exceeding the 140char limit of Twitter</li>
              </ul>
              ";
            
              echo "<table class='form-table mptadmin'>";
              
              if($headdisplay)
              {
                echo "<tr><td></td><td>".$headdisplay."</td></tr>";
              }
              if($bConnected)
              {
                $list_cat = get_categories();
                echo "<tr><th>".__("Categories to exclude:","mytrendingpost")."</th><td>";
                foreach ($list_cat as $cat)
                {
                  echo "<input type='checkbox' value='".$cat->cat_ID."' name='cats[]'";
                  if(in_array($cat->cat_ID, $cats))
                  {
                    echo " checked";
                  }
                  echo " /> ".$cat->cat_name."<br />";
                }
                echo "</td></tr>";
                echo "</table>";
                
                $this->setTweet(false);//va affiche le tweet mais ne va pas le creer
              }else{
                _e("You must be logged with a Twitter account in order to continue the settings", 'mytrendingpost');
                echo "</table>";
              }
              
            ?>
              
            </div>
          </div>
          <?php if($bConnected):?>
          <div class="submit">
            <input class="button-primary action" type="submit" name="update_myTrendingPostSettings"
              value="<?php _e('Update', 'mytrendingpost') ?>" />
          </div>
          <?php endif;?>
        </div>
      </div>
      </form>
    </div>
  
</div>