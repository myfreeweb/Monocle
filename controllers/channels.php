<?php
use BarnabyWalters\Mf2;

$app->get('/channels/?', function($format = 'html') use ($app) {
  if($user=require_login($app)) {
    $res = $app->response();

    $channels = ORM::for_table('channels')
      ->where('user_id', user_id())
      ->find_many();
    foreach($channels as $ch) {
      $ch['sources'] = ORM::for_table('channel_sources')
        ->join('feeds', ['channel_sources.feed_id','=','feeds.id'])
        ->where('channel_id', $ch['id'])->count();
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

    $channel = db\get_channel(user_id(), $id);

    if(!$channel) {
      $app->notFound();
    }

    $channels = db\get_user_channels(user_id());

    $entries = db\get_entries_for_channel($channel->id);

    ob_start();
    render('channel', [
      'title'    => 'Channel',
      'meta'     => '',
      'channel'  => $channel,
      'channels' => $channels,
      'entries'  => $entries
    ]);
    $res->body(ob_get_clean());
  }
});

$app->post('/channels/new', function() use($app) {
  if($user=require_login_json($app)) {
    $params = $app->request()->params();

    $channel = ORM::for_table('channels')->create();
    $channel->user_id = user_id();
    $channel->name = $params['name'];
    $channel->date_created = date('Y-m-d H:i:s');
    $channel->type = 'feeds';
    $channel->save();

    json_response($app, [
      'result' => 'ok'
    ]);
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
      $feed->name = friendly_url($params['url']);
      $feed->feed_url = $params['url'];
      $feed->save();
    }

    $channel = db\get_channel(user_id(), $params['channel_id']);

    if(!$channel) {
      // bad input
      json_response($app, [
        'result' => 'error'
      ]);
    } else {
      // Add the feed as a source for this channel, or update if it already exists
      db\add_source($channel->id, $feed->id, k($params, 'filter'));

      // Begin async processing of the feed to discover the push hub and initial content on the feed
      DeferredTask::queue('FeedTask', 'refresh_feed', $feed->id);

      json_response($app, [
        'result' => 'ok'
      ]);
    }
  }
});


$app->get('/channel/:id/settings', function($id) use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();
    $res = $app->response();

    $channel = db\get_channel(user_id(), $id);

    if(!$channel) {
      $app->notFound();
    }

    $feeds = db\get_feeds_for_channel($channel->id);

    ob_start();
    render('channel-settings', [
      'title' => 'Channel Settings',
      'meta' => '',
      'channel' => $channel,
      'feeds' => $feeds
    ]);
    $res->body(ob_get_clean());
  }
});

$app->post('/channel/:id/settings', function($id) use($app) {
  if($user=require_login($app)) {
    $params = $app->request()->params();
    $res = $app->response();

    $channel = db\get_channel(user_id(), $id);

    if(!$channel) {
      $app->notFound();
    }

    $feed = db\get_feed_for_user(user_id(), $params['feed_id']);

    if(!$feed) {
      $app->notFound();
    }

    switch($params['action']) {
      case 'set-name':
        $source = db\get_source($channel->id, $feed->id);
        $source->display_name = $params['name'];
        db\set_updated($source);
        $source->save();
        break;
      case 'refresh-feed':
        DeferredTask::queue('FeedTask', 'refresh_feed', $feed->id);
        break;
      case 'remove-feed':
        $source = db\get_source($channel->id, $feed->id);
        $source->delete();
        break;
    }

    json_response($app, [
      'result' => 'ok'
    ]);
  }
});

