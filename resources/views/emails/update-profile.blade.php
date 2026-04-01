<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{trans('emails/update-profile.Hotbtc_Exchange_Update_Profile')}} </title>
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
			<h4><b>{{trans('emails/update-profile.hello')}},</b></h4>
			<div style="font-size: 14px; color: #606060;">
				<!--<p>{{ trans('emails/msg.body_msg') }}</p>-->
				<p>{{trans('emails/update-profile.Your_Profile_is_updated')}}.</p><br>
				<p>{{trans('emails/update-profile.thanks')}},</p>
				<p>{{ config('app.name') }}</p>
			</div>		
		</div>
		<!--<div style="background:#606568; padding:20px;">

			<p style="text-align:center; color: #d1d1d1; margin-top: 10px;">{{ trans('emails/msg.btn_text') }}</p>

		
		</div>-->
		<div style="padding:10px; color: #fff; font-size: 13px; background:#031b3d; text-align:center;">
			{{trans('emails/update-profile.Hotbtc_All_right_reserved')}}
		</div>


	</div>
</body>  
</html>
