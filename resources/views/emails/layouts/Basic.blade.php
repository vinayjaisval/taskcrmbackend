<!DOCTYPE html>
<html lang="en">
<head>
  <title>@yield('title')</title>
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
		@yield('content')
	</div>
</body>  
</html> 

