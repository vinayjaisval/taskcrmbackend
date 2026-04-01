<!DOCTYPE html>
<html lang="en">
    <head>
  <title>{{ config('app.name') }} New Application Guidance</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: #DBDBDB; font-family: Helvetica Neue,Helvetica,Arial,sans-serif;">
    <div style="max-width:650px; margin:20px auto;">
        <div style="background:#e9f4ff; text-align:center;">
            <div style="background: url(https://overseaseducationlane.com/overseas/images/loginbg.jpg) no-repeat center; padding: 5px 0px">
                <img style="width:30%;" src="https://overseaseducationlane.com/overseas/images/oel.png" alt="">
            </div>
        </div>
        <div style="background:#fff; padding:20px;">
            <h4><b>{{__('Dear Admin')}},</b></h4>
            <div style="font-size: 14px; color: #606060;">
                <p>New lead came which is not assigned. Here is the new lead details using new application guidance</p>
                <p>Name: {{$firstName." ".$lastName}}</p>
                <p>Email ID: {{$emailId}}</p>
                <p>Phone number: +{{$mobileNo}}</p>
                 <p>Source: +{{$source}}</p>
                <p>Message: {{$msg}}</p>
               
                 <p>&nbsp;</p>
                <p>Warm Regards</p>
                <p>Overseas Education Lane(OEL)</p>
            </div>      
        </div>
        
    </div>
</body>  

</html>


