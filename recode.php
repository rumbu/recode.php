<?php
/**
  * @usage
  * php recode.php -p www
  */

// file listing
function browseCall($dir, $regex, $cb) {
 foreach(glob(chop($dir,'/')."/*") as $file) {
   if (is_dir($file)) browseCall($file, $regex, $cb);
   if (is_file($file) && preg_match($regex, basename($file)))
     call_user_func_array($cb, array($file));
 }
}
// change encoding
function myRecode($file) {
 $s = file_get_contents($file);
 $e = detectEncoding($s);
 echo "[".$e."] $file";
 if ('windows-1251'==$e) {
   $r = iconv('windows-1251','utf-8',$s);
   file_put_contents($file, $r);
   echo " [written ".strlen($r)." bytes]\n";
 }
 else echo "\n";
}
// encoding detector
function detectEncoding($string) {
 static $list = array('utf-8', 'windows-1251');
 foreach ($list as $item) {
   $sample = @iconv($item, $item, $string);
   if (md5($sample) == md5($string))
     return $item;
 }
 return null;
}

if (preg_match("~-p ([^ ]+)~", implode(' ',$argv), $matches)) {
 $path = $matches[1];
 browseCall($path, "~\.php$|\.tpl$|\.conf$|\.inc$|\.js$~", "myRecode");
}
?>