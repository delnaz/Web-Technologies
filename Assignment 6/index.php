<html>

<head>
	<Title> Homework 6 </title>
	<meta charset="UTF-8">
</head>


<body>
	<center>
		<b>WEATHER SEARCH</b>
		<br/><br/>
		<div style="border-width: 4px; border-style: double; border-color: black; width:400px; padding:8px; ">
		<table>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<tr><td>
			Location : <input maxlength="255" size="50" type="text" name="locid" id="locid" value="">
			<br></td>
			</tr>
			<tr><td>
			Location Type: <select name="location">
			<option select="selected" value="City">City</option>
			<option value="zipcode">Zip Code</option>
			</select><br>
			</td></tr>
			<tr><td>
			Temperature Unit: <input value="Fahrenheit" name="temperature" type="radio" checked="checked">Fahrenheit
			<input value="Celsius" name="temperature" type="radio">Celsius
			</td>
			</tr>
			<tr><td align="center">
			<input value="Search" name="Search" type="submit">
			</td></tr>
			</form>
		</table>
		</div>
	</center>
</body>
</html>

<?php if (isset($_POST["Search"]))
{
	$location = $_POST["locid"];
    $loctype = $_POST["location"];
    $tempunit = $_POST["temperature"];
	
if ($location ==""){
?>
<script type="text/javascript"> 
alert("Please enter a Location"); 
history.back();
</script>
<?php
    }
	else 
	{
               
        if ( $tempunit == 'Fahrenheit')
			{
				$unit = 'f';
			} 

        else 
			{
				$unit = 'c';
			}
		if ( $loctype == "City") 
		{
              
         if (preg_match("/\d{1}/", $location) ) 
			{
                    echo "Enter a valid city/zipcode \n";
                    return;             
            }  					  
			$loca=str_replace(",",  " " ,$location);
            $loca = urlencode($loca );
            $cityurl = "http://where.yahooapis.com/v1/places\$and(.q(%22".$loca."%22),.type(7));start=0;count=5?appid=kyQCwHvV34FovFNere_c3jch9sKoB2Afp4q9lOU4gonSN3papR8QoGXtNqLcAw";
            $xxml = simplexml_load_file($cityurl);
			$weatherResultCount = 0 ;
            $doc = new DOMDocument(); 
            $str = $xxml->asXML();
            $doc->loadXML($str); 
            $length = $doc->getElementsByTagName("woeid")->length; 
			if ( $length == 0) 
            {
				echo "<h2><center>Zero Results Found</center></h2> ";
            }  
			else 
			{
				echo "<br/>";
				echo "<center>";
				echo "$length result(s) for City  " .$location."";
				echo "</center>";
				echo " <br> <br><table border=1 align = center>";
				echo "<tr border=1><td><b>Weather</b></td><td><b>Temperature</b></td><td><b>City</b></td><td><b>Region</b></td><td><b>Country</b></td><td><b>Latitude</b></td><td><b>Longitude</b></td><td><b>Link to Details</b></td></tr>";
				for ( $i=0 ; $i < $length; $i++ )
				{
					$woeid =$xxml->place[$i]->locality1->attributes()->woeid;
					if($woeid == 0)
					{
						$woeid = $xxml->place[$i]->woeid;
                    }
					 $url = "http://weather.yahooapis.com/forecastrss?w=".$woeid."&u=".$unit; 
					$sxxml = simplexml_load_file($url, null, LIBXML_NOCDATA);
					$title = $sxxml->channel->title;
					$error = "Yahoo! Weather - Error"; 
					if (!strcmp ( $title , $error))
					{
						return;
					} 
					echo DisplayTable($url, $sxxml);
               echo "<script type='text/javascript'>document.getElementById('length').innerHTML = '".$weatherResultCount."';</script>\n"; 
				}
					echo "</table>";
			}
			}
			else  
			{
				
				if (!(preg_match ("/^\d{5}+$/", $location) ) )
				{
				    echo "Enter Valid Zipcode" ;
					return;
				}
				$zipurl = "http://where.yahooapis.com/v1/concordance/usps/".$location."?appid=kyQCwHvV34FovFNere_c3jch9sKoB2Afp4q9lOU4gonSN3papR8QoGXtNqLcAw";				
				$xml=simplexml_load_file($zipurl);
				if ( $xml == NULL)
				{
					return ;
				}

                        
            $woeid = $xml->woeid;
            
            if ( $woeid ==="")
            {
				echo "<center>Zero Results Found</center>";
            
				}  
				else
				{
					echo "<center>";
					echo "Result(s) for Zipcode".$location. "";
					echo "</center>";
					echo " <br> <br><table border=1; align =center>";
					echo "<tr><td border=1><b>Weather</b></td><td border=1><b>Temperature</b></td><td border=1><b>City</b></td><td border=1><b>Region</b></td><td border=1><b>Country</b></td><td border=1><b>Latitude</b></td><td border=1><b>Longitude</b></td><td border=1><b>Link to Details</b></td></tr>";
					$url = "http://weather.yahooapis.com/forecastrss?w=".$woeid."&u=".$unit; 
					$sxxml = simplexml_load_file($url, null, LIBXML_NOCDATA);
					$title = $sxxml->channel->title;
					$error = "Yahoo! Weather - Error"; 
					if (!strcmp ( $title , $error))
					{
						return;
					} 
					echo DisplayTable($url, $sxxml);
				}
            
            }  
		}
	}
 

function DisplayTable($url, $sxxml){
    $yweather = 0 ;
    $namespaces = $sxxml->getNameSpaces(true);
    if ( !isset($namespaces) || !isset($yweather) ){
    return ;
    }
    $yweather = $sxxml->channel->item->children($namespaces['yweather']);
    $geo = $sxxml->channel->item->children($namespaces['geo']);
	$location = $sxxml->channel->children($namespaces['yweather'])->location;
	$description= $sxxml->channel->item->description;
	$imgpattern = '/src="(.*?)"/i';
    preg_match($imgpattern, $description, $matches);
    $all_images['src'] = $matches[1];
	$weather='<a href="'.$url.'"\" target=\"_blank\"> <img src="'.$all_images['src'].'" alt="" onmouseover="this.src='.$yweather->condition->attributes()->text.'"/></a>';
    $temperature = $yweather->condition->attributes()->text." ".$yweather->condition->attributes()->temp."<sup>o</sup> ".$sxxml->channel->children($namespaces['yweather'])->units->attributes()->temperature;
    $city = $location->attributes()->city;
    $region = $location->attributes()->region;
    $country = $location->attributes()->country;
    $latitude = $geo->lat;
    $longitude = $geo->long;
    $link =  $sxxml->channel->link; 
    $link = "<a href=\"".$link."\" target=\"_blank\">Details</a>";
      if ( $weather == "") {
     $weather = "N/A";
     }
    if ($temperature == "") {
    $temperature = "N/A" ;
    }
     if ($city == "") {
    $city = "N/A" ;
    } 
    if ($region == "") {
    $region = "N/A" ;
    }
    if ($country == "") {
    $country = "N/A" ;
    }
    if ($latitude == "") {
    $latitude = "N/A" ;
    }
    if ($longitude == "") {
    $longitude = "N/A" ;
    }
    if ($link == "") {
    $link = "N/A" ;
    }
    return "<tr><td border=1>".$weather."</td><td border=1>".$temperature."</td><td border=1>".$city."</td><td border=1>".$region."</td><td border=1>".$country."</td><td border=1>".$latitude."</td><td border=1>".$longitude."</td><td border=1>".$link."</td></tr>\n";

}
?>
