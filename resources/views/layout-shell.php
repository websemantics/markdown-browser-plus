<!DOCTYPE html>
<html>
<head>
<title>Markdown Browser Plus</title>
<?php echo \Websemantics\FileIcons\FileIcons::includeCss(); ?>
<link rel="stylesheet" type="text/css" href="/resources/assets/css/style.css">
<style>
._content {padding-top: 10px;}
/**  Sidebar Settings */
._container { margin-left: 250px; }
._tree, ._sidebar { width: 250px; }
</style>
</head>
<body>
  <div class="_wrapper">
    <div class="_header"></div>
      <div class="_sidebar">
        <div id="tree" class="_tree-wrapper"><?php  
            /* Build the files tree of the content folder (CONTENT_DIR) */
            function fileTree($path = CONTENT_DIR, $relative = '') {
              $files = [];
              foreach (scandir($path) as $file) {
                if (strpos($file, '.') !== (int) 0) {
                  $files[$relative.$file] = is_dir("$path/$file") ? fileTree("$path/$file", $relative.$file.'/') : $file;
                }
              }
              return $files;
            }
            /* Parse the files tree and generate html */
            function parseTree($files, $icons, $nested = 0){
                $tree = '';
                $padding = $nested * 16 + 6;

                foreach ($files as $path => $value) {
                  $parts = explode('/',$path);

                  /* Sanitize name,  http://stackoverflow.com/questions/2103797/url-friendly-username-in-php */
                  $name = 'folder-'.strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($path, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));

                  if (is_array($value)) {
                      $tree .= "<li><input type='checkbox' name ='$name' id='$name'>".
                          "<label for='$name' style='padding-left:".$padding."px'><svg xmlns='http://www.w3.org/2000/svg' width='14' height='16' viewBox='0 0 14 16' class='folder icon'><path d='M13 4H9V3c0-.66-.31-1-1-1H1c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1V5c0-.55-.45-1-1-1zM8 4H1V3h7z'/><path class='open' d='M1 3v4.094h12V4.97H8V3H1z'/></svg>". end($parts) . "</label>".
                          '<ul>'.parseTree($value, $icons, $nested + 1).'</ul></li>';
                  } else {
                      $class_name = $icons->getClassWithColor($value);
                      // ($match = $icons->matchName($value)) ? $match->getClass(1) : 'text-icon';
                      $tree .= "<li><label  style='padding-left:".$padding."px'><a target='iframe' href='index.php/$path' onmouseup='document.getElementById(\"content\").scrollTop = 0'><i class='icon $class_name'></i>$value</a></label></li>";
                  }
                } 
                return $tree;  
                }
            
                /* Create an instance of Atom File Icons class, https://github.com/websemantics/file-icons */
                $icons = new \Websemantics\FileIcons\FileIcons();?>
                
          <div class='_tree'><?php echo parseTree(fileTree(), $icons); ?></div>
        </div>
    </div>
    <div class="_container">
      <main id="content" class="_content">
        <iframe  name="iframe" frameborder="0" style="overflow: hidden; height: 100%; width: 100%; position: absolute;" onload="this.style.height = this.contentWindow.document.body.scrollHeight + 'px'; " height="100%" width="100%" src="/index.php/README.md" ></iframe>
      </main>
    </div>
  </div>
</body>
</html>
