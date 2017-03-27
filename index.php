<?php  require __DIR__ . '/vendor/autoload.php';

/* Change content folder, i.e. '/docs' */
define('CONTENT_DIR', realpath(dirname(__FILE__)).'/');

$content = '';
$parser = new Parsedown();

if($url = trim(strpos($url = $_SERVER["REQUEST_URI"], 'index.php') > 0 ? substr($url,10) : $url, '/')) {

  /* Get the file path. If a folder, attempt to locate README.md  */
  $path = CONTENT_DIR . $url;
  $path .= @is_dir($path) ? "/README.md" : '';
  $extension = @pathinfo($path)['extension'];
  $content = @file_exists($path) ? @file_get_contents($path) : '';

  /* Wrap the content inside a suitable markdown based on mime type (text, image etc) */
  list($type, $subtype) = explode('/', @mime_content_type($path));

  switch($type){
    case "image" : 
      $content = $parser->text("![](/$url)");
    break;
    case "text" : 
        $content = $parser->text($extension === 'md' ? $content : "```$extension\n$content\n```" );
    break;
    default : 
        $content = "<pre>$content</pre>";
    break;
  }

  /* Otherwise, report missing content (404)! */
  $content = $content ? : $parser->text("## Error 404 :\n\nThe page is not found");
}

header('Content-type: text/html; charset=utf-8');

empty($content) ? include 'resources/views/layout-shell.php' : 
                  include 'resources/views/markdown-shell.php';