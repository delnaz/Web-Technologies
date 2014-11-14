<?php
header('Content-Type: text/xml;charset=utf-8');
       $location = $_GET['location'];
       $type = $_GET['type'];
       $tunit = $_GET['tempunit'];
	   
		   if (!strcmp ( $_GET["tempunit"] , "F")) {
            $unit = 'F';
        } else
        {
            $unit = 'C';
        }

		//echo ($unit);
		if ( $type == "city") 
		{
         					  
			$loca=str_replace(",",  " " ,$_GET["location"]);
            $loca = urlencode($loca );
            $cityurl = "http://where.yahooapis.com/v1/places\$and(.q(%22".$loca."%22),.type(7));start=0;count=5?appid=kyQCwHvV34FovFNere_c3jch9sKoB2Afp4q9lOU4gonSN3papR8QoGXtNqLcAw";
            
			$xxml = @simplexml_load_file($cityurl);
			
			$newResultCount = 0 ;
            $doc = new DOMDocument(); 
            $str = $xxml->asXML();
            $doc->loadXML($str); 
            $length = $doc->getElementsByTagName("woeid")->length; 
			
			if ( $length == 0) 
            {
				echo "<h2><center>Weather information cannot be found !</center></h2> ";
            }  
			else 
			{
			for ( $i=0 ; $i < $length; $i++ )
				{
					$woeid =$xxml->place[$i]->locality1->attributes()->woeid;
					if($woeid == 0)
					{
						$woeid = $xxml->place[$i]->woeid;
                    }
					  $url = "http://weather.yahooapis.com/forecastrss?w=".$woeid."&amp;u=".$unit; 
					  //echo($url);
					$sxxml = @simplexml_load_file($url, null, LIBXML_NOCDATA);
					$title = $sxxml->channel->title;
					$error = "Yahoo! Weather - Error"; 
					if (!strcmp ( $title , $error))
					{
						return;
					} 
					
					$newResultCount= DisplayTable($url, $sxxml,$unit);
				
					print_r($newResultCount);
                return $newResultCount;
				}
					
			}
			}
			else  
			{
				if($type=="zipcode")
				{
				
				$zipurl = "http://where.yahooapis.com/v1/concordance/usps/".$_GET["location"]."?appid=kyQCwHvV34FovFNere_c3jch9sKoB2Afp4q9lOU4gonSN3papR8QoGXtNqLcAw";				
				//echo($zipurl);
				$txml=@simplexml_load_file($zipurl);
				if ( $txml == NULL)
				{
					return ;
				}

                        
            $woeid = $txml->woeid;
         
            if ( $woeid ==="")
				{
				echo "<center>Zero Results Found</center>";
            
				}  
				else
				{
					$url = "http://weather.yahooapis.com/forecastrss?w=".$woeid."&amp;u=".$unit;
					//echo($url);
					//echo($unit);
					$sxxml =@simplexml_load_file($url, null, LIBXML_NOCDATA);
					$title = $sxxml->channel->title;
					$error = "Yahoo! Weather - Error"; 
					if (!strcmp ( $title , $error))
					{
						return;
					} 
					$newResultCount= DisplayTable($url, $sxxml,$unit);
				
					print_r($newResultCount);
                return $newResultCount;
				}
            }
            }  
function DisplayTable($url, $sxxml,$unit)
{
//echo ($url);
//$turl=$url;
//$units=$unit;
//echo($units);
    $yweather = 0 ;
    $namespaces = $sxxml->getNameSpaces(true);
	//echo $namespaces;
    if ( !isset($namespaces) || !isset($yweather) ){
    return ;
    }
    $yweather = $sxxml->channel->item->children($namespaces['yweather']);
	//echo($yweather);
    $geo = $sxxml->channel->item->children($namespaces['geo']);
	
	$location = $sxxml->channel->children($namespaces['yweather'])->location;
	
	
	$forecast_day = $sxxml->channel->item->children($namespaces['yweather'])->forecast->attributes()->day; 
    $forecast_low = $sxxml->channel->item->children($namespaces['yweather'])->forecast->attributes()->low; 
    $forecast_high = $sxxml->channel->item->children($namespaces['yweather'])->forecast->attributes()->high; 
    $forecast_text = $sxxml->channel->item->children($namespaces['yweather'])->forecast->attributes()->text;
	$w_link = (string)$sxxml->channel->link ;
	$description= $sxxml->channel->item->description;
	
	$imgpattern = '/src="(.*?)"/i';
    preg_match($imgpattern, $description, $matches);
    $all_images['src'] = $matches[1];
	$img = $all_images['src'];
	$weather='<a href="'.$url.'"\" target=\"_blank\"> <img src="'.$img.'" " title=\"".$temperature."\" alt=\"".$temperature."\"></a>';
    $temperature = $yweather->condition->attributes()->text." ".$yweather->condition->attributes()->temp."<sup>o</sup> ".$sxxml->channel->children($namespaces['yweather'])->units->attributes()->temperature;
    //$unit= $sxxml->channel->children($namespaces['yweather'])->units->attributes()->temperature;
	//echo($unit);
	//echo($temperature);
	$city = $location->attributes()->city;
    $region = $location->attributes()->region;
    $country = $location->attributes()->country;
    $latitude = $geo->lat;
    $longitude = $geo->long;
    $link =  $sxxml->channel->link; 
	//echo $w_link;
    $w_text = $yweather->condition->attributes()->text;
    $temp = $yweather->condition->attributes()->temp;

	for ( $i = 0; $i<=4 ;$i++) 
	{
    $forecast_day1[$i] = (string)$sxxml->channel->item->children($namespaces['yweather'])->forecast[$i]->attributes()->day; 
    $forecast_low1[$i] = (string)$sxxml->channel->item->children($namespaces['yweather'])->forecast[$i]->attributes()->low; 
    $forecast_high1[$i] = (string)$sxxml->channel->item->children($namespaces['yweather'])->forecast[$i]->attributes()->high; 
    $forecast_text1[$i] = (string)$sxxml->channel->item->children($namespaces['yweather'])->forecast[$i]->attributes()->text; 

    }
	
    $link = "<a href=\"".$link."\" target=\"_blank\">Details</a>";
	
	$xml = new SimpleXMLElement('<weather/>');
	$xml->addChild('feed',"$url");
   // $xml->addChild('feed', $url);
    $xml->addChild('link', "$w_link");
    $xml->addChild('location', "");
    $xml->location->addAttribute('city', "$city");
    $xml->location->addAttribute('region', "$region");
    $xml->location->addAttribute('country', "$country");
     $xml->addChild('units', "");
    $xml->units->addAttribute('temperature', "$unit");
    $xml->addChild('condition', "");
    $xml->condition->addAttribute('text', "$w_text");
    $xml->condition->addAttribute('temp', "$temp");
    $xml->addChild('img', "$img");
    $xml->addChild('forecast', "");
    $xml->forecast->addAttribute('day', "$forecast_day1[0]");
    $xml->forecast->addAttribute('low', "$forecast_low1[0]");
    $xml->forecast->addAttribute('high', "$forecast_high1[0]");
    $xml->forecast->addAttribute('text', "$forecast_text1[0]");
    $dxml = $xml->addChild('forecast', "");
    $dxml->addAttribute('day', "$forecast_day1[1]");
    $dxml->addAttribute('low', "$forecast_low1[1]");
    $dxml->addAttribute('high', "$forecast_high1[1]");
    $dxml->addAttribute('text', "$forecast_text1[1]");    
    $ddxml=$xml->addChild('forecast', "");
    $ddxml->addAttribute('day', "$forecast_day1[2]");
    $ddxml->addAttribute('low', "$forecast_low1[2]");
    $ddxml->addAttribute('high', "$forecast_high1[2]");
    $ddxml->addAttribute('text', "$forecast_text1[2]");
    $wxml=$xml->addChild('forecast', "");
    $wxml->addAttribute('day', "$forecast_day1[3]");
    $wxml->addAttribute('low', "$forecast_low1[3]");
    $wxml->addAttribute('high', "$forecast_high1[3]");
    $wxml->addAttribute('text', "$forecast_text1[3]");
    $qxml=$xml->addChild('forecast', "");
    $qxml->addAttribute('day', "$forecast_day1[4]");
    $qxml->addAttribute('low', "$forecast_low1[4]");
    $qxml->addAttribute('high', "$forecast_high1[4]");
    $qxml->addAttribute('text', "$forecast_text1[4]");


Header('Content-type: text/xml');
$result=$xml->asXML();
return $result;

    
	

}
?>
