<?php

function getRates()
{
  $xml_today_file = __DIR__.'/daily.xml';
  $xml_yesterday_file = __DIR__.'/prev-day.xml';

  // кеш файла с курсом валют на текущий день
  if (!is_file($xml_today_file) || filemtime($xml_today_file) < time() - 7200) {
      if ($xml_daily = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp')) {
          file_put_contents($xml_today_file, $xml_daily);
      }
  }

  // кеш файла с курсом валют на предыдущий день
  if (!is_file($xml_yesterday_file) || filemtime($xml_yesterday_file) < time() - 7200) {
      if ($xml_daily_prev = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date("d-m-Y", strtotime("-1 day")) )) {
          file_put_contents($xml_yesterday_file, $xml_daily_prev);
      }
  }

  // собираем массив с ключами и значениями валют
  $resultToday = array();
  foreach (simplexml_load_file($xml_today_file) as $elem) {
    $resultToday[strval($elem->CharCode)] = (float) strtr($elem->Value, ',', '.');
  }

  // собираем массив с ключами и значениями валют
  $resultYesterday = array();
  foreach (simplexml_load_file($xml_yesterday_file) as $elem) {
    $resultYesterday[strval($elem->CharCode)] = (float) strtr($elem->Value, ',', '.');
  }

  // Сравниваем курсы с предыдущим днём
  if ($resultToday['USD'] > $resultYesterday['USD']) {
    echo 'Курс доллара на ' . date("d-m-Y") . ' ' . '<h1>' . $resultToday['USD'] . '&#11014' . '</h1>';
  } else {
    echo 'Курс доллара на ' . date("d-m-Y") . ' ' . '<h1>' . $resultToday['USD'] . '&#11015' . '</h1>';
  }

  if ($resultToday['EUR'] > $resultYesterday['EUR']) {
    echo 'Курс евро на ' . date("d-m-Y") . ' ' . '<h1>' . $resultToday['EUR'] . '&#11014' . '</h1>';
  } else {
    echo 'Курс евро на ' . date("d-m-Y") . ' ' . '<h1>' . $resultToday['EUR'] . '&#11015' . '</h1>';
  }
}

getRates();

?>