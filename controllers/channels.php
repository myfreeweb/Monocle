<?php
use BarnabyWalters\Mf2;

$app->get('/channels/?', function($format = 'html') use ($app) {
  if($user=require_login($app)) {
    $res = $app->response();

    $channels = ORM::for_table('channels')
      ->where('user_id', $_SESSION['user_id'])
      ->find_many();
    foreach($channels as $ch) {
      $ch['sources'] = ORM::for_table('channel_sources')->where('channel_id', $ch['id'])->find_many();
    }

    ob_start();
    render('channels', array(
      'title'       => 'Channels',
      'meta'        => '',
      'channels'    => $channels
    ));
    $html = ob_get_clean();
    $res->body($html);
  }
});

$app->get('/channel/:id', function($format='html') use($app) {
  if($user=require_login($app)) {
    $res = $app->response();

    ob_start();
    render('channel', array(
      'title' => 'Channel',
      'meta' => ''
    ));
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

    $feeds = array();

    // Parse the URL and check for microformats h-entry posts, as well as linked rss or atom feeds
    $html = feeds\get_url($params['url']);
    $url = normalize_url($params['url']);

    if($html) {
      $mf2 = feeds\parse_mf2($html);

      // check if there are any h-entry posts
      $entries = Mf2\findMicroformatsByType($mf2, 'h-entry');
      if($entries && count($entries) > 0) {
        $feeds[] = array(
          'url' => $url,
          'display_url' => friendly_url($url),
          'type' => 'microformats2'
        );
      }

      // TODO: how to get the type attribute so we know if it's atom or rss
      $alternates = feeds\get_alternates($mf2);
      foreach($alternates as $alt) {
        $feeds[] = array(
          'url' => $alt['url'],
          'display_url' => friendly_url($alt['url']),
          'type' => 'atom'
        );
      }
    }

    json_response($app, array(
      'feeds' => $feeds
    ));
  }
});

$app->post('/channels/add', function($format='json') use($app) {
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

    // Create the default channel for this user if it doesn't exist yet
    $channel = ORM::for_table('channels')->where('user_id', $_SESSION['user_id'])->where('type','default')->find_one();
    if(!$channel) {
      $channel = db\new_channel($_SESSION['user_id'], 'Home', 'default');
      $channel->save();
    }

    // Add the feed as a source for this channel
    

    // Begin async processing of the feed to discover the push hub and initial content on the feed


    json_response($app, array(
      'result' => 'ok'
    ));
  }
});
