<?php
  // This example has 3 states.
  // Teaser: Will show the last 6 entries and a "More" button which goes to "List" state
  // List: Will show 10 entries per page, sorted by post date. Prints a Next and Previous button.
  //       Can also be queried with an category id.
  // Post: Shows one post and a "Back" button.

  // In this example the page '/blog/' will be the main page for the blog entries and posts.
  // Only the Teaser is shown on '/' (Homepage).

  $req = $_SERVER['REQUEST_URI'];

  $teaser = $req === "/" ? true : false; // show teaser on front-page
  $post = !!rex_get('post', 'integer', 0); // show post if a specific post was queried
  $list = $teaser === false && $post === false; // show list everywhere else

  $myBlogger = new Blogger(); // Blogger instance
?>


<?php
  // ONE POST/ENTRY
  if ($post) {
    // Shows a single post.

    echo '<article>';

    // Get data for entry.
    $id = rex_get('post', 'integer');
    $entry = $myBlogger->getEntry($id);

    // Preview Image
    $src = $entry['content'][1]['preview'];
    echo '<img src="/media/'.$src.'">';

    // Content
    echo '<div class="content">';
      // Print headline and text.
      echo '<h1>'.$entry['content'][1]['title'].'</h1>';
      echo $entry['content'][1]['text'];

      // Print gallery, if there is one.
      $list = $entry['content'][1]['gallery'];
      if ($list) {
        $list = explode(',', $list);
        echo '<div>';
        foreach ($list as $media) {
          $media = rex_media::get($media);

          // Note that 'imageblock' is not a default media type.
          $imgUrl = 'index.php?rex_media_type=imageblock&rex_media_file='.$media->getFileName();
          $imgAlt = $media->getValue('med_description');
          $imgTitle = $media->getTitle();

          echo '<a';
          echo ' href="'.$media->getUrl().'" data-lightbox="'.$id.'">';
          echo '<img src="'.$imgUrl.'" alt="'.$imgAlt.'" title="'.$imgTitle.'">';
          echo '</a>';
        }
        echo '</div>';
      }

    echo '</div>';
    
    echo '</article>';

    // Back button.
    echo ('<a onclick="javascript:history.back(); void 0;">Back</a>');

  } else if ($list) {
    // Show preview-image, headline and date of 10 entries.

    echo '<section>';

    // Set the page to 0 if there is none.
    $bloggerPage = rex_get('page', 'integer', 0);
    
    if ($bloggerPage < 0) {
      $bloggerPage = 0;
    }

    // Check if the url asked for a specific category id.
    // Alternatively you can use a string in the url and
    // check for the associated id of that category.
    $category = rex_get('category', 'integer', null);

    // Limit to 10 entries per page. 
    $limit = ($bloggerPage*10).", 10";

    $query = array(
      "limit" => $limit,
      "category" => $category,
      "includeOfflines" => false
    );

    // Get data for Entries.
    $pageEntries = $myBlogger->getEntriesBy($query);

    // Print the list
    foreach ($pageEntries as $entry) {
      $src = 'index.php?rex_media_type=blogger_preview&rex_media_file='.$entry['content'][1]['preview'];
      $date = DateTime::createFromFormat('Y-m-d H:i:s', $entry['postedAt']);
      $url = '/blog/artikel/?post='.$entry['id'];

      echo trim('
        <a href="'.$url.'">
          <div><img src="'.$src.'" /></div>
          <h2>'.$entry['content'][1]['title'].'</h2>
          <p>'.$date->format('j.m.Y').'</p>
          <p>Read more</p>
        </a>
      ');
    }

    echo '</section>';

    // This makes sure that the url for the page-buttons
    // will contain all parameters of the current page.
    // Example: If you want to look through all entries of a specific category or tag

    $paras = array('page' => $bloggerPage+1);
    $paras = array_merge($_GET, $paras);
    $nextUrl = rex_url::frontendController($paras);
    $nextUrl = str_replace("/index.php", "", $nextUrl);

    $paras = array('page' => $bloggerPage-1);
    $paras = array_merge($_GET, $paras);
    $prevUrl = rex_url::frontendController($paras);
    $prevUrl = str_replace("/index.php", "", $prevUrl);

    $nextButton = '<a href="'.$nextUrl.'">Next Page</a>';
    $prevButton = '<a href="'.$prevUrl.'">Previous Page</a>';

    // Don't display the 'Previous Page' button if there is none.      
    if ($bloggerPage <= 0) {
      $prevButton = '';
    }

    // Also check if a 'Next Page' button is required.
    // If there are no entries for the next Page, no button is needed.
    $nextLimit = ($bloggerPage+1) * 10;
    $nextQuery = $query;
    $nextQuery['limit'] = $nextLimit.", 1";
    $nextPage = $myBlogger->getEntriesBy($nextQuery);

    if (count($nextPage) === 0) {
      $nextButton = '';
    }

    // Output the buttons at the end of the page.
    echo '<div>'.$prevButton.PHP_EOL.$nextButton.'</div>';

  } else if ($teaser) {
    // Show 6 entries for a teaser-page.

    echo '<section>';

    // Get Date for last 6 entries.
    $pageEntries = $myBlogger->getLastEntries("0, 6");

    // Print each entry as teaser.
    foreach ($pageEntries as $entry) {
      $src = 'index.php?rex_media_type=blogger_preview&rex_media_file='.$entry['content'][1]['preview'];
      $date = DateTime::createFromFormat('Y-m-d H:i:s', $entry['postedAt']);
      $url = '/blog/artikel/?post='.$entry['id'];

      echo trim('
        <a href="'.$url.'">
          <div><img src="'.$src.'" /></div>
          <h2>'.$entry['content'][1]['title'].'</h2>
          <p>'.$date->format('j.m.Y').'</p>
          <p>Read more</p>
        </a>
      ');
    }

    echo '</section>';

    // Link to list-page.
    echo ('<a href="/blog">More Entries</a>');
  } // end TEASER
?>
