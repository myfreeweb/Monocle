<?php
use BarnabyWalters\Mf2;

$app->get('/channels/?', function($format = 'html') use ($app) {
  if($user=require_login($app)) {
    $res = $app->response();

    $channels = ORM::for_table('channels')
      ->where('user_id', $_SESSION['user_id'])
      ->find_many();
    foreach($channels as $ch) {
      $ch['sources'] = ORM::for_table('channel_sources')
        ->join('feeds', ['channel_sources.feed_id','=','feeds.id'])
        ->where('channel_id', $ch['id'])->find_many();
    }

    ob_start();
    render('channels', [
      'title'       => 'Channels',
      'meta'        => '',
      'channels'    => $channels
    ]);
    $html = ob_get_clean();
    $res->body($html);
  }
});

$app->get('/channel/:id', function($id) use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();
    $res = $app->response();

    $channel = db\get_channel($_SESSION['user_id'], $id);

    if(!$channel) {
      $app->notFound();
    }

    ob_start();
    render('channel', [
      'title' => 'Channel',
      'meta' => '',
      'channel' => $channel
    ]);
    $res->body(ob_get_clean());
  }
});

$app->post('/channels/discover', function($format='json') use($app) {
  if($user=require_login_json($app)) {
    $params = $app->request()->params();

    // $feeds = array(
    //   array('url' => 'http://pk.dev/', 'display_url' => friendly_url('http://pk.dev/'), 'type' => 'microformats2'),
    //   array('url' => 'http://pk.dev/articles.atom', 'display_url' => friendly_url('http://pk.dev/articles.atom'), 'type' => 'atom')
    // );

    $feeds = [];

    // Parse the URL and check for microformats h-entry posts, as well as linked rss or atom feeds
    $html = request\get_url($params['url']);
    $url = normalize_url($params['url']);

    if($html) {
      $mf2 = feeds\parse_mf2($html, $params['url']);

      // check if there are any h-entry posts
      $feed = feeds\find_feed_info($mf2);
      if($feed) {
        $feeds[] = [
          'url' => $url,
          'display_url' => friendly_url($url),
          'icon' => '<i class="icon-microformats"></i>',
          'enabled' => true
        ];
      }

      $alternates = feeds\get_alternates($mf2);
      foreach($alternates as $alt) {
        $feeds[] = [
          'url' => $alt['url'],
          'display_url' => friendly_url($alt['url']),
          'icon' => '<i class="fa fa-rss"></i>',
          'enabled' => false
        ];
      }
    }

    json_response($app, [
      'feeds' => $feeds
    ]);
  }
});

$app->post('/channels/add_feed', function($format='json') use($app) {
  if($user=require_login_json($app)) {
    $params = $app->request()->params();

    // Add the feed if it's not already in the database
    $feed = ORM::for_table('feeds')->where('feed_url', $params['url'])->find_one();
    if(!$feed) {
      $feed = db\new_feed();
      $feed->name = $params['url'];
      $feed->feed_url = $params['url'];
      $feed->save();
    }

    $channel = db\get_channel($_SESSION['user_id'], $params['channel_id']);

    if(!$channel) {
      // bad input
      json_response($app, [
        'result' => 'error'
      ]);
    } else {
      // Add the feed as a source for this channel, or update if it already exists
      db\add_source($channel->id, $feed->id, k($params, 'filter'));

      // Begin async processing of the feed to discover the push hub and initial content on the feed


      json_response($app, [
        'result' => 'ok'
      ]);
    }
  }
});
