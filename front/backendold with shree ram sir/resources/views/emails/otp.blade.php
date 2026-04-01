<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ config('app.name') }} OTP</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: #DBDBDB; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;">
	<div style="max-width:650px; margin:20px auto;">
		<div style="background:#e9f4ff; text-align:center;">
			<div style="background: url(https://overseaseducationlane.com/overseas/images/loginbg.jpg) no-repeat center; padding: 5px 0px">
				<img style="width:30%;" src="https://overseaseducationlane.com/overseas/images/oel.png" alt="">
			</div>
		</div>
		<div style="background:#fff; padding:20px;">
			<h4><b>{{__('Dear Sir/Madam')}},</b></h4>
			<div style="font-size: 14px; color: #606060;">
			    <p>The One Time Password (OTP)  is {{__('Verification OTP')}} {{$otp}}</p>
				<p>Please note that this OTP is valid only for one time and cannot be used later.</p></p></p>
				
				<p>Please do not share this One Time Password with anyone.</p>

<p>To know more, please visit FAQ</p></p>

<p>In case you have not requested for OTP, please contact the helpline at +(91) 813 0433 956</p>
				
				
				
				<p>Warm Regards</p>
				<p>Overseaseducationlane(OEL)</p>
			</div>		
		</div>
		
	</div>
</body>  

</html>


