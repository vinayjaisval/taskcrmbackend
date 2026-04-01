<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{trans('emails/forgot_password.Hotbtc_Exchange_Password_reset_email')}}</title>
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
		@php $link = url(config('app.url').route('password.reset', $token, false)); @endphp
		<div style="background:#fff; padding:20px;">
			<div style="font-size: 14px; color: #606060;">
				<p>{{trans('emails/forgot_password.hello')}},</p>
				<p>{{trans('emails/forgot_password.receiving_this_email')}}</p>
				<p>{{trans('emails/forgot_password.If_you_didnot_request')}}</p><br>
				<p>{{trans('emails/forgot_password.Thanks')}},</p>
				<p>{{trans('emails/forgot_password.Team_Hotbtc')}}</p>
			</div>		
		</div>		
		<div style="background:#fff; padding:20px;">
			<p style="text-align:center; color: #606568; margin-top: 10px;">{{trans('emails/forgot_password.Reset_your_password')}}</p>
			<a target="_blank" href="{{$link}}" style="background: #031b3d; font-size: 14px; color: #fff; border-radius: 5px; width: 120px; margin:auto; display:block; padding: 10px; text-transform: uppercase; text-decoration: none; text-align: center;">{{trans('emails/forgot_password.Reset_Password')}}</a>
		</div>
	
		<div style="padding:10px; color: #fff; font-size: 13px; background:#031b3d; text-align:center;">
				{{trans('emails/forgot_password.Hotbtc_All_right_reserved')}}
		</div>
	</div>
</body>  
</html>
