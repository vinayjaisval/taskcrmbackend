@extends('emails.layouts.Basic')
@section('title', 'University Update is Rejected')
@section('content')

<div style="background:#fff; padding:20px; text-align:center;">
	<h3 style="margin-top: 10px; text-align:center; font-size: 21px; font-weight: normal; text-transform: uppercase;">Subject : Your update to university details has been rejected by OEL !</h3>
	<div>{{$mail_message}}</div>
</div>
<div style="background:#0b2d56; text-align:center; padding:10px 5px;">
	<a style="text-decoration:none; color:#fff;" href="#">{{ config('app.name') }}</a>
</div>

@endsection