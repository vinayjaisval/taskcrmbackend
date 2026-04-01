<!DOCTYPE html>
<html lang="en">
<head>
  <title>Student Profile Created</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
   <style type="text/css">
		.button{
			display: inline-block;
	    margin-bottom: 0;
	    padding: .375rem .75rem;
	    font-size: 1rem;
	    line-height: 1.5;
	    font-size: 14px;
	    font-weight: 400;
	    cursor: pointer;
	    -webkit-user-select: none;
	    -moz-user-select: none;
	    -ms-user-select: none;
	    user-select: none;
	    text-align: center;
	    vertical-align: middle;
	    white-space: nowrap;
	    -webkit-border-radius: 4px;
	    border-radius: 4px;
	    background-color: #117a8b;
	    border-color: #10707f;
	    border-width: 3px;
	    letter-spacing: .05rem;
	    overflow: hidden;
	    border-radius: 4px;
	    text-decoration: none;
	    color: #fff !important;
		}
		.center{
			margin: auto;
			text-align: center;
		}
	</style>
</head>
<body style="font-family: 'Open Sans', sans-serif; line-height: 1.5;">
	<div style="max-width:650px; margin:15px auto; border: 1px solid #ccc;">
		<div style="background:#e9f4ff; text-align:center;">
			<div style="background: url({{ url('overseas/images/loginbg.jpg') }}) no-repeat center; padding: 5px 0px">
				<img style="width:30%;" src="{{ url('overseas/images/oel.png') }}" alt="">
			</div>
		</div>
		<div style="background:#fff; padding:20px; text-align:center;">
			<h3 style="margin-top: 10px; text-align:center; font-size: 21px; font-weight: normal; text-transform: uppercase;">Subject : Application Submitted</h3>
			<div>Your application has been successfully submitted.</div>
			<div style="font-size: 17px; text-align:center;">
				<p style="margin-bottom: 10px; text-align:center;">
				<p>
					<strong>Student Profile Created</strong><br>

					<p>A student profile has been created for your account. Use your phone number to login to OverseasEducationLane !</p>
					
					<div style="text-align: center;" class="center">
						<a href="{{ route('login') }}" class="button">Log In</a>
					</div>

				</p>
				</p>
			</div>
		</div>
		<div style="background:#0b2d56; text-align:center; padding:10px 5px;">
			<a style="text-decoration:none; color:#fff;" href="#">{{ config('app.name') }}</a>
		</div>
	</div>
</body>  
</html> 

