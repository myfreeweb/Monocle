<div id="channels">
  <ul>
    <? foreach($this->channels as $channel): ?>
      <li<?= $this->active_channel->id == $channel->id ? ' class="active"' : '' ?>><a href="/channel/<?= $channel['id'] ?>"><?= $channel['name'] ?></a></li>
    <? endforeach; ?>
  </ul>
</div>
<style type="text/css">
#channels ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  margin-top: 8px;
  padding-left: 4px;
  border-bottom: 1px #ccc solid;
}
#channels ul li {
  margin: 0;
  padding: 2px 8px;
  display: inline-block;

  background: #f9f9f9;

  border-top: 1px #ccc solid;
  border-left: 1px #ccc solid;
  border-right: 1px #ccc solid;

  -webkit-border-top-left-radius: 3px;
  -webkit-border-top-right-radius: 3px;
  -moz-border-radius-topleft: 3px;
  -moz-border-radius-topright: 3px;
  border-top-left-radius: 3px;
  border-top-right-radius: 3px;
}
#channels ul li:hover {
  background: #f2f2f2;
}
#channels ul li.active:hover {
  background: #949494;
}
#channels ul li a {
  text-decoration: none;
}
#channels ul li.active {
  background: #aaaaaa;
}
#channels ul li.active a {
  color: white;
}
</style>