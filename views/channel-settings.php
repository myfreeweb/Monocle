<div class="narrow">
  <?= partial('partials/header') ?>
  <?php if($this->channel->type != 'default'): ?>
    <h2><?= $this->channel->name ?></h2>
  <?php endif; ?>

  <?= partial('partials/add-feed-to-channel', [
    'channel' => $this->channel
  ]) ?>

  <?php foreach($this->feeds as $feed): ?>
    <div class="feed-settings-box" id="feed-<?= $feed['id'] ?>">

      <div class="action-buttons">
        <button class="btn btn-info refresh-feed" data-feed-id="<?= $feed['id'] ?>"<?= $feed['refresh_in_progress'] ? ' disabled="disabled"' : '' ?>>
          <i class="fa fa-refresh fa-spin" style="<?= $feed['refresh_in_progress'] ? '' : 'display:none;' ?>"></i>
          Refresh Feed
        </button>
        <button class="btn btn-danger remove-feed" data-feed-id="<?= $feed['id'] ?>">Remove</button>
      </div>

      <h4>
        <span class="name" id="feed-name-<?= $feed['id'] ?>" data-feed-id="<?= $feed['id'] ?>"><?= db\feed_display_name($feed) ?></span>
        <a href="javascript:edit_feed_name(<?= $feed['id'] ?>)" class="edit-name"><i class="fa fa-pencil"></i></a>
      </h4>

      <form class="form-horizontal" role="form">

        <div class="form-group">
          <div class="col-sm-3 control-label">Feed URL</div>
          <div class="col-sm-9 control-value">
            <a href="<?= $feed['feed_url'] ?>"><?= $feed['feed_url'] ?></a>
          </div>
        </div>

        <?php if($feed['filter']): ?>
          <div class="form-group">
            <div class="col-sm-3 control-label">Filter</div>
            <div class="col-sm-9 control-value">
              <?= $feed['filter'] ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="form-group">
          <div class="col-sm-3 control-label">Last Retrieved</div>
          <div class="col-sm-9 control-value"><?= $feed['last_retrieved'] ?></div>
        </div>

        <div class="form-group">
          <div class="col-sm-3 control-label">Latest Post Date</div>
          <div class="col-sm-9 control-value"><?= $feed['last_post_date'] ?></div>
        </div>

        <div class="form-group">
          <div class="col-sm-3 control-label"><a href="http://indiewebcamp.com/PubSubHubbub" target="_blank">PubSubHubbub</a></div>
          <div class="col-sm-9 control-value">
            <?php if($feed['push_hub_url']): ?>
              <table class="push-details">
                <tr>
                  <td>Hub:</td>
                  <td><?= $feed['push_hub_url'] ?></td>
                </tr>
                <tr>
                  <td>Topic:</td>
                  <td><?= $feed['push_topic_url'] ?></td>
                </tr>
                <tr>
                  <td>Subscribed:</td>
                  <td><?= $feed['push_subscribed'] ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                  <td>Last Ping Received:</td>
                  <td><?= $feed['push_last_ping_received'] ?></td>
                </tr>
                <tr>
                  <td>Expiration:</td>
                  <td><?= $feed['push_expiration'] ?></td>
                </tr>
              </table>
            <?php else: ?>
              No PuSH hub was found
            <?php endif; ?>
          </div>
        </div>

      </form>
    </div>
  <?php endforeach; ?>

</div>
<style type="text/css">
.feed-settings-box {
  border: 1px #ccc solid;
  background: #fff;

  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;

  -webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.2);
  -moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.2);
  box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.2);

  padding: 6px;
  margin-bottom: 10px;
}

.feed-settings-box h4 {
  margin-left: 10px;
}

.feed-settings-box .action-buttons {
  float: right;
}

.feed-settings-box a.edit-name {
  color: #bbb;
}
.feed-settings-box a.edit-name:hover {
  color: #999;
}

.feed-settings-box .control-label {
  font-weight: bold;
}
.feed-settings-box .control-value {
  margin-top: 7px;
}

.feed-settings-box .push-details td {
  padding: 3px;
}

</style>
<script>
$(function(){
  $(".feed-settings-box .name").on('keydown', function(e){
    if(e.keyCode == 13) {
      var feed_id = $(this).data('feed-id');
      $("#feed-name-"+feed_id).attr("contentEditable","false");
      $.post("/channel/<?= $this->channel->id ?>/settings", {
        action: 'set-name',
        feed_id: feed_id,
        name: $("#feed-name-"+feed_id).text()
      }, function(){
        $("#feed-"+feed_id+" .edit-name").show();
      });
      return false;
    }
  });
  $(".refresh-feed").click(function(){
    $(this).find('.fa').show();
    $(this).attr("disabled","disabled");

    var feed_id = $(this).data('feed-id');
    $.post("/channel/<?= $this->channel->id ?>/settings", {
      action: 'refresh-feed',
      feed_id: feed_id
    }, function(){

    });
  });
  $(".remove-feed").click(function(){
    var feed_id = $(this).data('feed-id');
    $.post("/channel/<?= $this->channel->id ?>/settings", {
      action: 'remove-feed',
      feed_id: feed_id
    }, function(){
      window.location = window.location;
    });
  });
});
function edit_feed_name(id) {
  $("#feed-name-"+id).attr("contentEditable","true").focus();
  $("#feed-"+id+" .edit-name").hide();
}
</script>