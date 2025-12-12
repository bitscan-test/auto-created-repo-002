<?php
// 日付書式
define('DATE_TYPE','Y年n月j日');

function get_japanese_weekday($weekday_number) {
  
  $weekday[0] = '日';
  $weekday[1] = '月';
  $weekday[2] = '火';
  $weekday[3] = '水';
  $weekday[4] = '木';
  $weekday[5] = '金';
  $weekday[6] = '土';
  
  if (isset($weekday[$weekday_number])) {
    return $weekday[$weekday_number];
  } else {
    return '';
  }
  
}
?>