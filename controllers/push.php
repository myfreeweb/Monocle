<?php

// PuSH hubs call this to verify subscriptions and send pings on new content
$app->get('/push/feed/:hash', function($hash) use($app){
  $params = $app->request()->params();

  $feed = ORM::for_table('feeds')->where('hash',$hash)->find_one();

  $app->response()['Content-Type'] = 'text/plain';


  if(!$feed) {

    $app->response()->status(404);
    echo 'Feed not found';

  } else {
    // PHP apparently converts . to _ in params

    if(k($params, 'hub_mode') == 'subscribe') {
      // The hub is sending us a subscription verification request. We need to respond with the challenge.

      $feed->push_subscribed = 1;
      if(k($params, 'hub_lease_seconds'))
        $feed->push_expiration = date('Y-m-d H:i:s', strtotime(time() + $params['hub_lease_seconds']));
      $feed->save();

      echo $params['hub_challenge'];

    } else {
      // No other GET request is valid for the callback URL

      $app->response()->status(400);
      echo 'Bad request';

    } 
  }
});

$app->post('/push/feed/:hash', function($hash) use($app){
  $params = $app->request()->params();

  $feed = ORM::for_table('feeds')->where('hash',$hash)->find_one();

  $app->response()['Content-Type'] = 'text/plain';

  if(!$feed) {

    $app->response()->status(404);
    echo 'Feed not found';

  } else {

    $feed->push_last_ping_received = date('Y-m-d H:i:s');
    $feed->save();

    DeferredTask::queue('FeedTask', 'refresh_feed', $feed->id);
    echo 'ok';

  }
});
