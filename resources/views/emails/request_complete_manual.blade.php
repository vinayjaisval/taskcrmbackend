<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{trans('emails/request_complete_manual.Hotbtc_Exchange_Support')}} </title>
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
			<h4><b>{{trans('emails/request_complete_manual.Dear_Member')}},</b></h4>
			<div style="font-size: 14px; color: #606060;">
				<p>{{trans('emails/request_complete_manual.Thank_you_for_contacting')}} </p>
				<p>{{trans('emails/request_complete_manual.Thank_you_for_using_Hotbtc')}}</p>
				<br>
				<p>{{trans('emails/request_complete_manual.Regards')}},</p>
				<p>{{trans('emails/request_complete_manual.Team_Hotbtc')}}</p>
			</div>		
		</div>
		<div style="padding:10px; color: #fff; font-size: 13px; background:#031b3d; text-align:center;">
			{{trans('emails/request_complete_manual.donot_reply_this_email')}}
			<br>
			{{trans('emails/request_complete_manual.Hotbtc_All_right_reserved')}}
		</div>

</div>
</body>  
</html>


