# Google Map CSV Plotter
A web application to aid in tracking current customers/users.  The appliation accepts a .csv of 4 columns (Street, City, State and Zipcode) and connects to Google Maps API to obtain the coordinates and then plot the locations on a single map.  For added functionality you may also enter a location into an input field and upload an additional csv so that you may check if your customers/users are sharing the same geographic area.


## Getting Started
1. Obtain a Google Maps API Key, instructions can be found [here](https://developers.google.com/maps/documentation/javascript/get-api-key).
2. Update this line: 
```
<script src='//maps.google.com/maps/api/js?key=[API KEY]'></script>
```
replacing [API KEY] with the key obtained.
3. Update $upload_location (location where your csv files will be stored) to reflect your file structure.
4. Testing.
4. Style to meet your needs.


## Authors
Ryan Dool