<?php

function get_mime($filename) {
  if (function_exists("mime_content_type"))
    return mime_content_type($filename);
  $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
  $mime = finfo_file($finfo, $filename);
  finfo_close($finfo);
  return $mime;
}
