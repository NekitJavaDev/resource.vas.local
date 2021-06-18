<?php
// PulsarNightTime - планировщик задач Windows - с 23.02 до 05.58 каждую минуту, каждый день

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://resource.vas.local/meters/cron_refresh/pulsar_night');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($curl);
curl_close($curl);
    
echo $out;