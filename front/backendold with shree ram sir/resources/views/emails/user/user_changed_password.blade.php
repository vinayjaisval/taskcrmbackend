@extends('emails.layouts.Basic')
@section('title', 'An update to your program is approved by OEL')
@section('content')

<div style="background:#fff; padding:20px; text-align:center;">
	<h3 style="margin-top: 10px; text-align:center; font-size: 21px; font-weight: normal; text-transform: uppercase;">Subject : Password has been changed successfully !</h3>
	<div>{{$mail_message}}</div>
	<p>In case you have not changed your account password, please contact the helpline at +(91) 813 0433 956</p>
</div>
<div style="background:#0b2d56; text-align:center; padding:10px 5px;">
	<a style="text-decoration:none; color:#fff;" href="#">{{ config('app.name') }}</a>
</div>

@endsection