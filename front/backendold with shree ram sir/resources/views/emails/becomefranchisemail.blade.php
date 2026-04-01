<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ config('app.name') }} </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: #DBDBDB; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;">
	<div style="max-width:650px; margin:20px auto;">
		<div style="background:#e9f4ff; text-align:center;">
			<div style="background: url({{ url('overseas/images/loginbg.jpg') }}) no-repeat center; padding: 5px 0px">
				<img style="width:30%;" src="{{ url('overseas/images/oel.png') }}" alt="">
			</div>
		</div>
		<div style="background:#fff; padding:20px;">
			<h4 style="padding:20px;"><b>Dear Sir/Madam,</b></h4>
			<div style="font-size: 14px; color: #606060; padding:20px;">
				<p>You have got an email from : {{ $name }} </p>
				
                    Franchise details: <br>
                    Name: {{ $name }} <br>
                    Email: {{ $email }} <br>
                    Phone: {{ $phone }} <br>
                    Subject: {{ $subject }} <br>
                    Message: {{ $user_query }} <br><br>
                    Thanks With Regards<br>
                    {{ $name }}    
                
			</div>		
		</div>
		
	</div>
</body>  

</html>