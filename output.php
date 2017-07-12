<?php

/**
 * Example
 */

?>

<div class="container">
<div class="col-xs-12">
<?php

$blogger = new rex_blogger(5);

if ($blogger->is_single_entry()) {
    $entry = $blogger->get_single_entry();

        $buffer = '';
        $buffer .= '<div class="col-md-6">';
        $buffer .= '<h4>'.$entry->getHeadline().'</h4>';
        $buffer .= '<p>'.$entry->getContent().'</p>';
        $buffer .= '</div>';

    echo $buffer;

    echo '<div class="col-xs-12"><pre>';
    var_dump($entry);
    echo '</pre></div>';


} else {
    echo '<div class="col-xs-12">';
    echo '<a href="'.$blogger->get_url_previous_page().'">PREVIOUS</a>';
    echo ' | ';
    echo '<a href="'.$blogger->get_url_next_page().'">NEXT</a>';
    echo '</div>';

    $counter = 0;

    foreach ($blogger->get_entries_blog_page() as $key=>$entry) {
 
        $buffer = '';
        $buffer .= '<div class="col-md-6">';
        $buffer .= '<h4>'.$entry->getHeadline().'</h4>';
        $buffer .= $entry->getContent();
        $buffer .= '<a href="'.$entry->getUrl().'">MORE</a>';
        $buffer .= '<hr class="visible-sm">';
        $buffer .= '</div>';
        echo $buffer;
        if ($counter%2 == 1) echo '<div class="col-xs-12 hidden-sm"><div class="col-md-6"><hr></div><div class="col-md-6"><hr></div></div>';
        $counter++;
    }

}

?>
</div>
</div>