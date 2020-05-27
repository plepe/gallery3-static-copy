<?php
$url = 'https://example.com/gallery/';
$remove_levels = 2; // remove top 2 levels (because a sub-sub-album should be saved)

function clear_page ($filename) {
  global $url;
  global $remove_levels;
  $remove_levels_preg = "/^" + str_repeat("\\.\\.\\/", $remove_levels) + "/";

  $dom = new DOMDocument();
  $dom->loadHTMLFile($filename);

  foreach (array('g-login-link', 'g-quick-search-form', 'g-site-menu', 'g-comments', 'g-exif-data-link', 'g-view-menu') as $to_remove) {
    if ($node = $dom->getElementById($to_remove)) {
      $node->parentNode->removeChild($node);
    }
  }

  $uls = $dom->getElementsByTagName('ul');
  foreach ($uls as $ul) {
    if ($ul->getAttribute('class') === 'g-breadcrumbs') {
      for ($i = 0; $i < $remove_levels; $i++) {
        $first_li = $ul->getElementsByTagName('li')[0];
        $ul->removeChild($first_li);
      }

      $first_li = $ul->getElementsByTagName('li')->item(0);
      $first_li->setAttribute('class', 'g-first');

      $as = $first_li->getElementsByTagName('a');
      if ($as->length) {
        $as[0]->setAttribute('href', '../');
      }
    }
  }

  $logo = $dom->getElementById('g-logo');
  $logo->setAttribute('href', $url);

  if ($remove_levels > 0) {
    $nodes = $dom->getElementsByTagName('link');
    foreach ($nodes as $node) {
      $url = $node->getAttribute('href');
      $url = preg_replace($remove_levels, '', $url);
      $node->setAttribute('href', $url);
    }

    $imgs = $dom->getElementsByTagName('img');
    foreach ($imgs as $img) {
      $url = $img->getAttribute('src');
      $url = preg_replace($remove_levels, '', $url);
      $img->setAttribute('src', $url);
    }

    $nodes = $dom->getElementsByTagName('script');
    foreach ($nodes as $node) {
      $url = $node->getAttribute('src');
      if ($url) {
        $url = preg_replace($remove_levels, '', $url);
        $node->setAttribute('src', $url);
      }
    }
  }

  $dom->saveHTMLFile($filename);
}

clear_page($argv[1]);
