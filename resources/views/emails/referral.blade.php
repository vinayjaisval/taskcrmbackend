<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{trans('emails/referral.Hotbtc_Exchange_Referral')}}</title>
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
			<h4><b>{{trans('emails/referral.hello')}},</b></h4>
			<div style="font-size: 14px; color: #606060;">
				<p>{{trans('emails/referral.invited_you')}}.  </p>
				<p>{{trans('emails/referral.fastest_growing_network')}} </p>
				<p>{{trans('emails/referral.Happy_Trading')}}</p>
				<p>{{trans('emails/referral.Team_Hotbtc')}}</p>
			</div>		
		</div>
		<div style="background:#fff; padding:20px;">

			<p style="text-align:center; color: #606568; margin-top: 10px;">{{trans('emails/referral.click_below_link')}} </p>

			<a href="{{ url('register?where-joining-code=').$user->profile->referral_code }}" style="background: #031b3d; font-size: 14px; color: #fff; border-radius: 5px; width: 120px; margin:auto; display:block; padding: 10px; text-transform: uppercase; text-decoration: none; text-align: center;">{{trans('emails/referral.Register')}}</a>

		</div>
		<div style="padding:10px; color: #fff; font-size: 13px; background:#031b3d; text-align:center;">
			{{trans('emails/referral.Hotbtc_All_right_reserved')}}
		</div>
	</div>
</body>  

</html>


