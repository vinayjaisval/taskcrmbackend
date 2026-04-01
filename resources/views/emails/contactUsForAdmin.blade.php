<!DOCTYPE html>
<html lang="en">
<head>
  <title>Contact Us</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Open Sans', sans-serif; line-height: 1.5;">
	<div style="max-width:650px; margin:15px auto; border: 1px solid #ccc;">
		{{--<div style="background-color: #0b2d56; box-shadow: 1px 0px 20px rgba(0,0,0,0.08); text-align:center;">
			<div style="padding: 10px 0px;background:#111;">
				<img style="width:200px;height:80px;" src="{{ URL::to('/')}}/dochplus/img/logo.png" alt="">
			</div>
		</div>--}}
		<div style="background:#e9f4ff; text-align:center;">
			<div style="background: url({{ URL::to('/')}}/overseas/images/loginbg.jpg) no-repeat center; padding: 5px 0px">
				<img style="width:30%;" src="{{ URL::to('/')}}/overseas/images/oel.png" alt="">
			</div>
		</div>
		
		<div style="background:#fff; padding:20px; text-align:center;">
			<div style="font-size: 17px; text-align:center;">
				<p>Hi, </p>
				<p>You have got a new message </p>

				<p>Name : {{ $details['name'] }}</p>
				<p>Email : {{ $details['email'] }}</p>
				<p>Subject : {{ $details['subject'] }}</p>
				<p>Message : {{ $details['message'] }}</p>

			</div>
			<p>{{ __('Thanks & Regard') }},</p>
			<p>{{ config('app.name') }} {{ __('Team') }}</p>

			<p style="color:blue;text-align:center">** {{ __('This is an auto-generated email. Please do not reply to this email') }}.</p>

		</div>
		<div style="background:#111; text-align:center; padding:10px 5px;">
			<a style="text-decoration:none; color:#fff;" href="#">{{ config('app.name') }}</a>
		</div>
	</div>
</body>  
</html> 
