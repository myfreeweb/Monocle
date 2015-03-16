<div class="entry">
  <div class="author">
    <div class="photo">
      <img src="<?= $this->entry->author_photo ?>" width="48">
    </div>
    <a class="url" href="<?= $this->entry->author_url ?>"><?= friendly_url($this->entry->author_url) ?></a>
    <div class="name"><?= $this->entry->author_name ?></div>
  </div>
  <div class="clear"></div>
  <div class="content">
    <?= $this->entry->content ?>
  </div>
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
