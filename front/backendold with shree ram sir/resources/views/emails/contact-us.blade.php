<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{trans('emails/contact-us.Hotbtc_Exchange_Enquiry')}} </title>
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
        <h4><b>{{trans('emails/contact-us.hello')}}</b></h4>
        <div style="font-size: 14px; color: #606060;">
            <p>{{trans('emails/contact-us.Request_for_enquiry')}}</p>
            <h3>{{trans('emails/contact-us.Enquiry_Details')}}</h3>
            <p>{{trans('emails/contact-us.Name')}}: {{ $data[0] }}</p>
            <p>{{trans('emails/contact-us.Email')}}: {{ $data[1] }}</p>
            <p>{{trans('emails/contact-us.Enquiry')}}: {{ $data[2] }}</p>
            <br>
            <p>{{trans('emails/contact-us.Thanks')}},</p>
            <p>{{trans('emails/contact-us.Team_Hotbtc')}}</p>
        </div>
    </div>
    <div style="padding:10px; color: #fff; font-size: 13px; background:#031b3d; text-align:center;">
        {{trans('emails/contact-us.Hotbtc_All_right_reserved')}}
    </div>

</div>
</body>
</html>
