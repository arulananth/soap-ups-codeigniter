
<!DOCTYPE html>
<html>
<head>
<title>UPS DEMO</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
<link rel="stylesheet" href="assets/main.css" />
<script type="text/javascript" src="assets/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="assets/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/jquery.steps.min.js"></script>
</head>
<body>
<div class="container">
<div class="row">
<div class="col-sm-8 col-sm-offset-2">
<div class="page-header">
<div class="alert alert-info" role="alert">
<h4>UPS DEMO Shipping Rate</h4>

</div>
</div>
<form id="signupForm" method="post" class="form-horizontal" action="">
	<h3>Shipper Details</h3>
	<fieldset>

<?=$shipper?>

</fieldset>
<!--- step 1 -->
<h3>From Details</h3>
<fieldset>

<?=$from?>

</fieldset>
<!-- step 2-->
<h3>Delivery Details</h3>
<fieldset>

<?=$to?>
</fieldset>
<!-- step 3-->
<h3>Dimension</h3>
<fieldset>

<?=$extra?>

<div class="form-group">
<div class="col-sm-5 col-sm-offset-4">
<div class="checkbox">
<label>
<input type="checkbox" id="agree" name="agree" value="agree" />Please agree to our policy
</label>
</div>
</div>
</div>

<br clear="all">
<div class="col-sm-12 alert alert-success hide"></div>

</fieldset>
<!-- step 3 -->
</form>
</div>
</div>
</div>
<script type="text/javascript">
	
var form = $("#signupForm");

form.steps({
    headerTag: "h3",
    bodyTag: "fieldset",
    transitionEffect: "slideLeft",
    onStepChanging: function (event, currentIndex, newIndex)
    {
        form.validate().settings.ignore = ":disabled,:hidden";
        return form.valid();
    },
    onFinishing: function (event, currentIndex)
    {
        form.validate().settings.ignore = ":disabled";
        return form.valid();
    },
    onFinished: function (event, currentIndex)
    {
        $(".alert-success").removeClass("hide");
				$(".alert-success").html("<p>Loading...</p>");
				$.ajax({
					url:'welcome/soap?action=rate&type=PHP',method:'POST',
					data:$("#signupForm").serialize(),
					success:function(msg){
                        msg=JSON.parse(msg);
                        if(msg){
                     var charge=msg['RatedShipment'][0]['TotalCharges'];
                     $(".alert-success").removeClass("hide").html(charge.CurrencyCode+" "+charge.MonetaryValue)}
                     else
                     {
                     	$(".alert-success").removeClass("hide").html("Try Again check all of your input")
                     }
					},error:function(msg){
						console.log(msg)
						$(".alert-success").removeClass("hide").html("Try Again check all of your input")
					}
				})
    }
});
		$.validator.setDefaults( {
			submitHandler: function () {
				
				let json ={
 "UPSSecurity": {
 "UsernameToken": {
 "Username": "maxim_develop",
 "Password": "Development1"
 },
 "ServiceAccessToken": {
 "AccessLicenseNumber": "1D5D93ACAAC0A1EA"
 }
 },
 "RateRequest": {
 "Request": {
 "RequestOption": "Rate",
 "TransactionReference": {
 "CustomerContext": "Southsoft"
 }
 },
 "Shipment": {
 "Shipper": {
 "Name": "Arul Ananth",
 "ShipperNumber": "9994667490",
 "Address": {
 "AddressLine": [
 "Port Street 5 ",
 "Main road",
 "Colachel"
 ],
 "City": "Colachel",
 "StateProvinceCode": "TN",
 "PostalCode": "629251",
 "CountryCode": "IND"
 }
 },
 "ShipTo": {
 "Name": "Manju",
 "Address": {
 "AddressLine": [
 "Holy Cross road",
 "punninager",
 "Nagercoil"
 ],
 "City": "Nagercoil",
 "StateProvinceCode": "TN",
 "PostalCode": "629252",
 "CountryCode": "IND"
 }
 },
 "ShipFrom": {
 "Name": "Arul Ananth",
 "ShipperNumber": "9994667490",
 "Address": {
 "AddressLine": [
 "Port Street 5 ",
 "Main road",
 "Colachel"
 ],
 "City": "Colachel",
 "StateProvinceCode": "TN",
 "PostalCode": "629251",
 "CountryCode": "IND"
 }
 },
 "Service": {
 "Code": "03",
 "Description": "UPS Ground"
 },
 "Package": {
 "PackagingType": {
 "Code": "02",
 "Description": "Rate"
 },
 "Dimensions": {
 "UnitOfMeasurement": {
 "Code": "IN",
 "Description": "inches"
 },
 "Length": "5",
 "Width": "4",
 "Height": "3"
 },
 "PackageWeight": {
 "UnitOfMeasurement": {
 "Code": "Lbs",
 "Description": "pounds"
 },
 "Weight": "1"
 }
 },
 "ShipmentRatingOptions": {
 "NegotiatedRatesIndicator": ""
 }
 }
 }
}
				
			}
		});
         
        let valid={};
        <?php
        foreach($valid as $v)
        {
          ?>
        	valid['<?=$v?>']="required";
        	<?php
             }
        ?>
        valid['agree']="required";
		$( document ).ready( function () {
			$( "#signupForm" ).validate( {
				rules:valid,
				
				errorElement: "em",
				errorPlacement: function ( error, element ) {
					// Add the `help-block` class to the error element
					error.addClass( "help-block" );

					if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.parent( "label" ) );
					} else {
						error.insertAfter( element );
					}
				},
				highlight: function ( element, errorClass, validClass ) {
					$( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
				},
				unhighlight: function (element, errorClass, validClass) {
					$( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
				}
			} );

			
		} );
	</script>
</body>
</html>
