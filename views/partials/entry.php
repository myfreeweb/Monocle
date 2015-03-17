<div class="entry-context"<?= $this->entry->in_reply_to_url ? '' : ' style="display:none;"' ?>>
  In reply to <a href="<?= $this->entry->in_reply_to_url ?>"><?= friendly_url($this->entry->in_reply_to_url) ?></a>
</div>
<div class="entry<?= $this->entry->in_reply_to_url ? ' has-context' : '' ?>">
  <div class="author">
    <div class="photo">
      <img src="<?= $this->entry->author_photo ?>" width="48">
    </div>
    <a class="url" href="<?= $this->entry->author_url ?>"><?= friendly_url($this->entry->author_url) ?></a>
    <div class="name"><?= $this->entry->author_name ?></div>
  </div>
  <div class="clear"></div>
  <? if($this->entry->name): ?>
    <h2 class="name"><?= $this->entry->name ?></h2>
  <? endif; ?>
  <? if($this->entry->summary): ?>
    <div class="summary"><?= $this->entry->summary ?></div>
  <? endif; ?>
  <? if($this->entry->content): ?>
    <div class="content"><?= $this->entry->content ?></div>
  <? endif; ?>
  <? if($this->entry->audio_url): ?>
    <div class="audio">
      <audio src="<?= $this->entry->audio_url ?>" controls="controls" style="width: 100%"></audio>
    </div>
  <? endif; ?>
  <div class="meta">
    <ul>
      <li><a href="<?= $this->entry->url ?>"><?= friendly_date($this->entry->date_published, $this->entry->timezone_offset) ?></a></li>
      <? 
        foreach(['comment','like','repost','rsvp'] as $type) {
          if($this->entry->{"num_".$type."s"}) {
            echo '<li>' . response_icon($type) . ' ' . $this->entry->{"num_".$type."s"} . ' ' . pluralize($type, $this->entry->{"num_".$type."s"}) . '</li>';
          }
        }
      ?>
    </ul>
  </div>
</div>
<div class="entry-actions" id="entry-actions-<?= $this->entry->id ?>">

  <div class="button-row">
    <button class="action action-like" title="Like this post" data-action="like" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-star"></i></button>
    <button class="action action-repost" title="Repost this" data-action="repost" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-retweet"></i></button>
    <button class="action action-bookmark" title="Create a bookmark" data-action="bookmark" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-bookmark"></i></button>
    <button class="action action-reply" title="Write a reply" data-action="reply" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-reply"></i></button>
  </div>
  <div class="result" style="display: none;">
    <div class="summary"></div>
    <div class="raw" style="display: none;">
      <b>Error!</b>
      <p>Raw response from your site:</p>
      <pre></pre>
    </div>
  </div>

</div>
