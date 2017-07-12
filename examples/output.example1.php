<?php
  $isFrontPage = (rex_article::getCurrent()->getUrl() == '/');
  define('BLOG_URL', '/blog/');


  $templateTeaser = function($entry) {

    $isBlogPage = (rex_article::getCurrent()->getUrl() == BLOG_URL);

    $getPreviewContent = function($content) {
      // $content = preg_replace('/&nbsp;/', ' ', $content);
      preg_match_all('/<p>(.*?)<\/p>/', $content, $content);
      $content = array_map(function($item) {
        $item = htmlentities($item);
        $item = str_replace('&nbsp;', ' ', $item);
        $item = trim($item);
        $item = html_entity_decode($item);
        return $item;
      }, $content[1]);

      $content = array_filter($content);

      $content = $content[1] ?: $content[0];

      $pos = strpos($content, '.');
      $pos = strpos($content, '.', $pos+1) ?: strlen($content);
      $content = substr($content, 0, $pos);
      return '<p>'.$content.'<span> ...</span></p>';
    };

    $headline = $entry->getHeadline();
    $category = $entry->getCategory();

    $preview = $entry->getPreview();
    $preview = 'index.php?rex_media_type=block&rex_media_file='.$preview;

    $date = new DateTime($entry->getPostDate());
    $dateD = $date->format('d.M');
    $dateY = $date->format('Y');

    $content = $getPreviewContent($entry->getContent());

    // show blog url on all other pages
    if ($isBlogPage) {
      $url = $entry->getUrl();
    } else {
      $url = BLOG_URL.$entry->getUrl();
      $url = preg_replace('/\/\//', '/', $url);
    }
    
    return '
      <div class="fx-blog-teaser">
        <h3>'.$headline.'</h3>
        <h6>'.$category.'</h6>
        <div class="img"><img src="'.$preview.'"></div>
        <div class="time">
          <span class="date">'.$dateD.'</span><span class="year">'.$dateY.'</span>
        </div>
        <div class="content">'.$content.'</div>
        <a href="'.$url.'">Weiterlesen</a>
      </div>
    ';
  };


  $templateFull = function($entry) {

    $headline = $entry->getHeadline();
    $category = $entry->getCategory();

    $preview = $entry->getPreview();
    $preview = 'index.php?rex_media_type=block&rex_media_file='.$preview;

    $date = new DateTime($entry->getPostDate());
    $dateD = $date->format('d.M');
    $dateY = $date->format('Y');

    $content = $entry->getContent();

    return '
      <div class="fx-blog-teaser">
        <h3>'.$headline.'</h3>
        <h6>'.$category.'</h6>
        <div class="img"><img src="'.$preview.'"></div>
        <div class="time">
          <span class="date">'.$dateD.'</span><span class="year">'.$dateY.'</span>
        </div>
        <div class="content">'.$content.'</div>
      </div>
    ';
  };


  $blogger = new Blogger(6);
  $mainContent = '';

  // all buttons
  $nextUrl = $blogger->getUrlNext();
  $prevUrl = $blogger->getUrlPrev();

  $yearList = '';

  $navButtons = '';
  $moreButton = '<a href="'.BLOG_URL.'">Mehr</a>';
  $nextButton = '<a href="'.$nextUrl.'">Nächste Seite</a>';
  $prevButton = '<a href="'.$prevUrl.'">Vorherige Seite</a>';
  $backButton = '<a href="#" onclick="history.back()">Zurück</a>';

  if ($blogger->isSingleEntry()) {

    // if only one specific entry is supposed to show up
    $entry = $blogger->getSingleEntry();
    $mainContent = $templateFull($entry);

    // show back button for single entries
    $navButtons .= $backButton;

  } else {

    // show the correct buttons
    if (!$isFrontPage) {
      $navButtons .= $prevButton;
      $navButtons .= $nextButton;

      // list dates
      $dates = Blogger::getAllMonths();
      foreach ($dates as $date) {
        $year = $date['year'];
        $month = DateTime::createFromFormat('!m', $date['month']);
        $month = $month->format('M');
        $yearList .= ('
          <li class="col-md-2 col-sm-3 col-xs-12">
            <a href="/blog/?bloggerYear='.$year.'&bloggerMonth='.$date['month'].'">'.$month.'&nbsp;-&nbsp;'.$year.'</a>
          </li>
        ');
      }
    } else {
      $navButtons .= $moreButton;
    }

    foreach ($blogger->getEntriesBlogPage() as $key => $entry) {
      $mainContent .= $templateTeaser($entry);
    }
  }
?>

<div class="fx-blog col-xs-12">

  <!-- year list -->
  <ul class="year-list col-xs-12">
    <?= $yearList ?>
  </ul>

  <?= $mainContent ?>
  <div class="fx-blog-teaser buttons"><?= $navButtons ?></div>
</div>