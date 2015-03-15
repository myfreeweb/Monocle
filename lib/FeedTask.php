<?php
class FeedTask {

  public static function refresh_feed($feed_id) {
    $feed = db\get_feed($feed_id);

    echo "Refreshing feed ".$feed->feed_url." ($feed_id)\n";

    

  }

}
