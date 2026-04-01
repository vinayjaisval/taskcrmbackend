<!DOCTYPE html>
<html lang="en">
<head>
  <title>Registration Successful</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: #DBDBDB; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;">
	<div style="max-width:650px; margin:20px auto;">
	<div style="background:#e9f4ff; text-align:center;">
			<div style="background: url({{ url('hotbtc/img/loginbg.jpg') }}) no-repeat center; padding: 5px 0px">
				<img style="width:30%;" src="{{ url('hotbtc/img/logo.png') }}" alt="">
			</div>
		</div>
		<div style="background:#fff; padding:20px;">
			<h4><b>Hello,</b></h4>
			<div style="font-size: 14px; color: #606060;">
				<p>Congratulations !! Your registration is successful.</p>
				<p>Thanks,</p>
				<p>Overseas team</p>
			</div>		
		</div>
		{{--<div style="background:#fff; padding:20px;">

			<p style="text-align:center; color: #031b3d; margin-top: 10px;">To Activate Your Account Click Here </p>

			<a href="{{ route('email-verification.check', $user->profile->verification_token) }}" style="background: #031b3d; font-size: 14px; color: #fff; border-radius: 5px; width: 120px; margin:auto; display:block; padding: 10px; text-transform: uppercase; text-decoration: none; text-align: center;">{{trans('emails/verify.Verify')}}</a>

		</div>
		<div style="padding:10px; color: #fff; font-size: 13px; background:#031b3d; text-align:center;">
			{{trans('emails/verify.Hotbtc_All_right_reserved')}}
		</div>--}}
	</div>
</body>  

</html>


