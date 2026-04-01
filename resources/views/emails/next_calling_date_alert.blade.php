<!DOCTYPE html>
<html lang="en">
<head>
  <title>Next Calling Date Alert</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Open Sans', sans-serif; line-height: 1.5;">
	<div style="max-width:650px; margin:15px auto; border: 1px solid #ccc;">
		<div style="background:#e9f4ff; text-align:center;">
			<div style="background: url({{ url('overseas/images/loginbg.jpg') }}) no-repeat center; padding: 5px 0px">
				<img style="width:30%;" src="{{ url('overseas/images/oel.png') }}" alt="">
			</div>
		</div>
		<div style="background:#fff; padding:20px; text-align:center;">
			<h3 style="margin-top: 10px; text-align:center; font-size: 21px; font-weight: normal; text-transform: uppercase;">Subject : Next Calling Date Alert</h3>
			<div>You have {{ $student_count }} students waiting to be called. Visit <a href="{{ route('lead_dashboard') }}">Lead Dashboard</a> to see your upcoming call list.</div>
		</div>
		<div style="background:#0b2d56; text-align:center; padding:10px 5px;">
			<a style="text-decoration:none; color:#fff;" href="#">{{ config('app.name') }}</a>
		</div>
	</div>
</body>  
</html> 

