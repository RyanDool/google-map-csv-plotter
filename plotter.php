<?php
	$csv = array();
	$addy_arr = [];
	$addy_arr_two = [];
	$upload_location = '[PATH TO CSV UPLOAD LOCATION]';
	if(isset($_POST["submit"])){
		if(!empty($_POST["location_address"]) || !empty($_POST["location_address_two"])){
			$fac_array = [];
			$location_address = "";
			if(!empty($_POST["location_address"])){
				$location_address = $_POST["location_address"];
				$location_address = str_replace(" ", "+", $location_address);
				array_push($fac_array, $location_address);
			}
			if(!empty($_POST["location_address_two"])){
				$location_address = $_POST["location_address_two"];
				$location_address = str_replace(" ", "+", $location_address);
				array_push($fac_array, $location_address);
			}
			$fac_array = json_encode($fac_array);
			echo "<script> var fac_array = ". $fac_array . ";\n </script>";
		}
 
		if(empty($_FILES["file"]["type"])){
			echo "No file selected";
		}else{
			$storagename = $_FILES["file"]["name"];
			move_uploaded_file($_FILES["file"]["tmp_name"], $upload_location . $storagename);
			$storedin = $upload_location . $_FILES["file"]["name"];
			$handle = fopen($storedin, "r");
			if(($handle = fopen($storedin, 'r')) !== FALSE){
				set_time_limit(0);
				$row = 0;
				while(($line = fgetcsv($handle)) !== FALSE){
					$line = str_replace(" ", "+", $line);
					$immmp = implode("+", $line);
					array_push($addy_arr, $immmp);
				}
				fclose($handle);
				$js_array = json_encode($addy_arr);
				echo "<script> var javascript_array = ". $js_array . ";\n </script>";
 
				if(!empty($_FILES["file_two"]["type"])){
					$storagename_two = $_FILES["file_two"]["name"];
					move_uploaded_file($_FILES["file_two"]["tmp_name"], $upload_location . $storagename_two);
					$storedin_two = $upload_location . $_FILES["file_two"]["name"];
					$handle_two = fopen($storedin_two, "r");
					if(($handle_two = fopen($storedin_two, 'r')) !== FALSE){
						set_time_limit(0);
						$row_two = 0;
						while(($line_two = fgetcsv($handle_two)) !== FALSE){
							$line_two = str_replace(" ", "+", $line_two);
							$immmp_two = implode("+", $line_two);
							array_push($addy_arr_two, $immmp_two);
						}
						fclose($handle_two);
						$js_array_two = json_encode($addy_arr_two);
						echo "<script> var javascript_array_two = ". $js_array_two . ";\n </script>";
						echo "<script type='text/javascript'> window.onload = function(){ myCustomers(); } </script>";
					}
				}else{
					echo "<script type='text/javascript'> window.onload = function(){ myCustomers(); } </script>";
				}
			}
		}
	}
?>
<html lang="en-US" class="external-links ua-brand-icons">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
		<meta name="MobileOptimized" content="width">
		<meta name="HandheldFriendly" content="true">
		<title>Plotting Tool</title>
		<link href='//fonts.googleapis.com/css?family=Open Sans:300,400,600,700' rel='stylesheet' type='text/css'>
		<!-- <script src='//maps.google.com/maps/api/js?key=[API KEY]'></script> -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<link href='styles.css' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<div id="plotter_container">
			<div id="plotter_inner">  
				<header></header>		 
				<div id="plooter" class="plotter_container">
				    <form action="" method="POST" enctype="multipart/form-data" class="plotter_form wrapper">
				        <fieldset>
				            <input type="text" name="location_address" id="location_address" class="plotter_input" />
				            <label for="location_address" class="plotter_label">OPTIONAL: Facility Address (Street City, State Zip)<img src="http://maps.google.com/mapfiles/ms/icons/red-pushpin.png"></label>
				            <input type="file" name="file" value="file" id="file_select" class="plotter_input" />
				            <label for="file_select" class="plotter_label">File Containing Customer Addresses (must be CSV)</label>
				            <div id="hideme">
				                <input type="text" name="location_address_two" id="location_address_two" class="plotter_input" />
				                <label for="location_address_two" class="plotter_label">OPTIONAL: Facility Address (Street City, State Zip)<img src="http://maps.google.com/mapfiles/ms/icons/blue-pushpin.png"></label>
				                <input type="file" name="file_two" value="file_two" id="file_select_two" class="plotter_input" />
				                <label for="file_select_two" class="plotter_label">File Containing Customer Addresses (must be CSV)</label>
				            </div>
				            <div class="multiple_warning">
				                <span>Do you have an additional customer list you would like plotted on the same map?
				                <input type="checkbox" class="additional_locations" value="1" name="additional" /></span>
				                <strong>NOTE:</strong> this feature should be used sparingly, its inteded use is for facilities in close proximity with one another.
				            </div>
				            <input type="submit" name="submit" value="Submit" class="plotter_submit" />
				        </fieldset>
				    </form>
				    <div id="map_canvas"></div>
				</div>
			</div>
        </div>
        <footer></footer>
        <script>
        	$(function(){
        		$('.additional_locations').click(function(){
        			if($('input.additional_locations').prop('checked') == false) {
        				$('input#location_address_two').val('');
        				$('input#file_select_two').val('');
        			}
        			$('#hideme').slideToggle();
        		})
        	})
        	function myCustomers(){
        		let address_index = 1;
        		let address_two_index = 0;
        		let fac_addy_index = 0;
        		let geocoder;
        		let map;
        		let elevator;
        		let myOptions = {
        				zoom: 2,
        				center: new google.maps.LatLng(0, 0),
        		};
        		map = new google.maps.Map($('#map_canvas')[0], myOptions);
        		let addresses = javascript_array;
        		let bounds = new google.maps.LatLngBounds();
        		if(typeof fac_array !== 'undefined'){
        			let fac_addresses = fac_array;
        			addfacilitymarkers();
        		}
        		if(typeof javascript_array_two !== 'undefined'){
        			let addresses_two =  javascript_array_two;
        			addMapMarker(addresses_two, 'blue');
        		}
        		addMapMarker(addresses, 'red');

        		//
        		function addfacilitymarkers(){
        			let fac_address = fac_addresses[fac_addy_index];
        			if(fac_address.length){
        				$.getJSON('//maps.googleapis.com/maps/api/geocode/json?address='+fac_address, null, function (data){
        					if(data.results.length > 0){
        						let fp = data.results[0].geometry.location;
        						let latlng = new google.maps.LatLng(fp.lat, fp.lng);
        						bounds.extend(latlng);
        						if(fac_addy_index == 0){
        							icon = "http://maps.google.com/mapfiles/ms/icons/red-pushpin.png";
        						}
        						if(fac_addy_index == 1){
        							icon = "http://maps.google.com/mapfiles/ms/icons/blue-pushpin.png";
        						}
        						new google.maps.Marker({
        							position: latlng,
        							map: map,
        							icon: new google.maps.MarkerImage(icon)
        						});
        						map.fitBounds(bounds);
        					}
        					nextFacAddress();
        				});
        			}
        		}
         
        		function nextFacAddress(){
        			fac_addy_index++;
        			if(fac_addy_index < fac_addresses.length){
        				addfacilitymarkers();
        			}
        		}
        
        		function addMapMarker(address_arr, pincolor){
        			let address = address_arr[address_arr_index];
        			if(address.length){
        				$.getJSON('//maps.googleapis.com/maps/api/geocode/json?address='+address, null, function(data){
        					if(data.results.length > 0){
        						let p = data.results[0].geometry.location;
        						let latlng = new google.maps.LatLng(p.lat, p.lng);
        						bounds.extend(latlng);
        						icon = "http://maps.google.com/mapfiles/ms/icons/"+pincolor+"-dot.png";
        						new google.maps.Marker({
        							position: latlng,
        							map: map,
        							icon: new google.maps.MarkerImage(icon)
        						});
        						map.fitBounds(bounds);
        					}
        					address_arr_index++;
        					if(address_arr_index < address_arr.length){
        						addMapMarker(address_arr, pincolor);
        					}
        				});
        			}else{
        				address_arr_index++;
        				if(address_arr_index < address_arr.length){
        					addMapMarker(address_arr, pincolor);
        				}
        			}
        		}
        	}
        </script>
    </body>
</html>