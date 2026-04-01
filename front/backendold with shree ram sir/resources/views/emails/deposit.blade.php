<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{trans('emails/deposit.Hotbtc_Exchange_Enquiry')}}</title>
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
			<h4><b>{{trans('emails/deposit.hello')}},</b></h4>
			<div style="font-size: 14px; color: #606060;">
				<p>{{trans('emails/deposit.A_deposit_of')}} @php echo $ct["amount"].' '.$ct["coin"];  @endphp {{trans('emails/deposit.has_been_received_in_wallet')}}.  </p>
				<br>
				<p>{{trans('emails/deposit.Thanks')}},</p>
				<p>{{trans('emails/deposit.Team_Hotbtc')}}</p>
			</div>		
		</div>
		<div style="padding:10px; color: #fff; font-size: 13px; background:#031b3d; text-align:center;">
			{{trans('emails/deposit.Hotbtc_All_right_reserved')}}
		</div>

</div>
</body>  
</html>


