<script>
	$("html").css("background","#000000");
</script>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script> 
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
 $(document).ready(function(){

  var mapOptions = {
       zoom: 10,
       mapTypeId: google.maps.MapTypeId.ROADMAP,
       center: new google.maps.LatLng(41.06000,28.98700)
     };

  var map = new google.maps.Map(document.getElementById("map"),mapOptions);

  var geocoder = new google.maps.Geocoder();  

     $(function() {
         $("#r_location").autocomplete({
         
           source: function(request, response) {

          if (geocoder == null){
           geocoder = new google.maps.Geocoder();
          }
             geocoder.geocode( {'address': request.term }, function(results, status) {
               if (status == google.maps.GeocoderStatus.OK) {

                  var searchLoc = results[0].geometry.location;
               var lat = results[0].geometry.location.lat();
                  var lng = results[0].geometry.location.lng();
                  var latlng = new google.maps.LatLng(lat, lng);
                  var bounds = results[0].geometry.bounds;

                  geocoder.geocode({'latLng': latlng}, function(results1, status1) {
                      if (status1 == google.maps.GeocoderStatus.OK) {
                        if (results1[1]) {
                         response($.map(results1, function(loc) {
                        return {
                            label  : loc.formatted_address,
                            value  : loc.formatted_address,
                            bounds   : loc.geometry.bounds
                          }
                        }));
                        }
                      }
                    });
            }
              });
           },
           select: function(event,ui){
      var pos = ui.item.position;
      var lct = ui.item.locType;
      var bounds = ui.item.bounds;

      if (bounds){
       map.fitBounds(bounds);
      }
           }
         });
     });   
 });
</script>
<a href="#" id="r_title">Welcome on Flammup.com</a>
<a href="index.php?option=login" id="r_login">Login</a>

<div align="center" id="fire_girl">
	<img src="<?php echo $Config_live_site; ?>/images/register.png" class="dragon"/>
</div>
<div id='register_form'>
<form name='content' method='post' id='content' action="index.php?task=register" target="_self">
	<div class='register_area' id='register_type'>
		Register as:&nbsp;&nbsp;<span id='s_buyer' class="r_user">Buyer</span>/<span class="r_user" id="s_seller">Seller</span>
		<input type="hidden" name="h_user" id="h_user" value=""/>
	</div>
	<table cellspacing="5" border="0" id="register_table">
	<tr>
		<td>Nickname</td>
		<td><input type="text" name="r_nickname" id="r_nickname" class="register_text" value="" /></td>
	</tr>
	<tr>
		<td>Birth date</td>
		<td>
		<select id='birth_year'>
			<?php
				for ($i=1900; $i<2012; $i++){
					if ($i == 1970) 
						echo "<option value='".$i."' selected>".$i."</option>";
					else 
						echo "<option value='".$i."'>".$i."</option>";
				}
			?>
		</select>		
		<select id='birth_month'>
			<?php
				for ($i=1; $i<13; $i++){
					echo "<option value='".$i."'>".$i."</option>";
				}
			?>
		</select>		
		<select id='birth_day'>
			<?php
				for ($i=1; $i<32; $i++){
					echo "<option value='".$i."'>".$i."</option>";
				}
			?>
		</select>		
		
		</td>
	</tr>
	<tr>
		<td>Sex</td>
		<td align="center"><span class='r_sex' id='r_male'>Male</span>/<span class='r_sex' id='r_female'>Female</span></td>
		<input type="hidden" name="h_sex" id="h_sex" value="" />
	</tr>
	<tr>
		<td>location</td>
		<td><input type="text" name="r_location" class="register_text" id="r_location" value="" /></td>
	</tr>
	<tr>
		<td>Mail</td>
		<td><input type="text" name="r_mail" id="r_mail" class="register_text" value="" /></td>
	</tr>
	<tr>
		<td>Password</td>
		<td><input type="password" name="r_password" id="r_password" class="register_text" value="" /></td>
	</tr>
	<tr style="display:none;" id="confirm_area">
		<td>Confirm</td>
		<td><input type="password" name="r_confirmpassword" id="r_confirmpassword" class="register_text" value="" /></td>
	</tr>

	</table>
</form>
</div>
<a href="#" id="r_register">Register for free</a>
<a href="#" id="r_help">How it works?</a>
<div id="map"></div>
<script>
	
	$("#r_password").bind("focusout", function () {
		var password = $("#r_password").val();
		if (!password)
		{
			return false;
		}
		$("#confirm_area").css("display","");
		$("#r_confirmpassword").focus();
	});
	$("#confirm").click( function () {
		document.content.submit();
	});
	$("#r_register").bind("click", function () {
		var user = $("#h_user").val();
		if (!user)
		{
			alert("Select User Type!");
			return false;
		}
		var nickname = $("#r_nickname").val();
		if (!nickname)
		{
			alert("Type your nickname!");
			return false;
		}
		var sex = $("#h_sex").val();
		if (!sex)
		{
			alert("Select your sex.");
			return false;
		}
		var location = $("#r_location").val();
		if (!r_location)
		{
			alert("Type your location!");
			return false;
		}
		var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
		var emailAddress = $("#r_mail").val();
		if (!emailAddress)
		{
			alert("Type your mail.");
			return false;
		}
		 var valid = emailRegex.test(emailAddress);
		  if (!valid) {
			alert("Invalid e-mail address");
			return false;
		  }
		var password = $("#r_password").val();
		if (!password)
		{
			alert("Type your password!");
			return false;
		}
		var confirm = $("#r_confirmpassword").val();
		if (!confirm)
		{
			alert("Type your confirm password!");
			return false;
		}
		if (confirm != password)
		{
			alert("Password and Confirm password is different!");
			return false;
		}
		$("#content").submit();

	});
	$("#r_male").bind("click", function () {
		$(this).css("color","#F35D0B");
		$("#h_sex").val("male");
		$("#r_female").css("color","#FFFFFF");
	});
	$("#r_female").bind("click", function () {
		$(this).css("color","#F35D0B");
		$("#h_sex").val("male");
		$("#r_male").css("color","#FFFFFF");
	});
	$("#s_buyer").bind("click", function () {
		$(this).css("color","#F35D0B");
		$("#h_user").val("buyer");
		$("#s_seller").css("color","#FFFFFF");
	});
	$("#s_seller").bind("click", function () {
		$(this).css("color","#F35D0B");
		$("#h_user").val("seller");
		$("#s_buyer").css("color","#FFFFFF");
	});

</script>
