<?php
// MercuryWorkTimeOfDay - планировщик задач Windows - с 6.00 до 23.00 каждый час, каждый день

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://resource.vas.local/meters/cron_refresh/mercury');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($curl);
curl_close($curl);

echo $out;