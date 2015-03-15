<?php
namespace feeds;
use BarnabyWalters\Mf2;

function parse_mf2(&$html, $base) {
  $parser = new \mf2\Parser($html, $base);
  return $parser->parse();
}

function get_rels(&$data) {
  if($data && array_key_exists('rels', $data)) {
    return $data['rels'];
  } else {
    return [];
  }
}

function get_alternates(&$data) {
  if($data && array_key_exists('alternates', $data)) {
    return $data['alternates'];
  } else {
    return [];
  }
}

// Given a parsed microformat data structure, find the feed on the page.
// This is meant to follow
// * http://indiewebcamp.com/feed#How_To_Consume
// * http://microformats.org/wiki/h-feed#Parsing
// Returns an array:
// [
//   'properties' => [ list of mf2 properties of the h-feed ],
//   'entries' => [ list of h-entry items of the feed ]
// ]
function find_feed_info(&$data) {

  // tantek.com : h-card => h-feed => h-entry
  // snarfed.org : h-feed => h-entry
  // aaronparecki.com : h-entry

  $properties = [];
  $entries = [];

  // Find the first h-feed
  $feeds = Mf2\findMicroformatsByType($data, 'h-feed');
  if(count($feeds)) {
    $feed = $feeds[0];

    $properties = $feed['properties'];
    $entries = Mf2\findMicroformatsByType($feed['children'], 'h-entry', false);

    return [
      'properties' => $properties,
      'entries' => $entries
    ];

  } else {
    // This is an implied feed if there are h-entry posts found at the top level

    $entries = Mf2\findMicroformatsByType($data['items'], 'h-entry', false);

    if(count($entries)) {
      return [
        'properties' => [],
        'entries' => $entries
      ];
    }

  }

  return false;
}
