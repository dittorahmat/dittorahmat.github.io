<?php
error_reporting(0);
include '../pengaturan.php';
header('Content-type: application/xml; charset="ISO-8859-1"', true);
$uri            = trim ($_SERVER['REQUEST_URI'], '/');
$subdomen		= explode('.',trim($uri));

$data 			= dirname(__FILE__).'/aliexpress-sitemap/'.$sitemap.'.txt';
$c 				= file($data, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$mapx = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n";
$mapx .= '<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
      http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\r\n";

foreach($c as $d){

	if(!empty($d)) {
		$mapx .= '<url>
		  <loc>https://'.$subdomen[0].'.blogspot.com/?pid='.$d.'&amp;key='.$TrackingID.'</loc>
		  <priority>0.8</priority>
		  <changefreq>Monthly</changefreq>
		</url>'."\r\n";
	}
}
$mapx .= '</urlset>';
echo $mapx;
exit;
?>