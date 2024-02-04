@php
	$medal = "gold.jpeg";
	if ($certificate->score < 85) {
		$medal = "silver.jpeg";
	}
@endphp
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Staff Get Salaries</title>
	<style type="text/css">
      * {
          font-family: "XBRiyaz", sans-serif !important;

      }


      .container {
          text-align: center;
          background-image: url("{{public_path('images/global/certificate_'.$medal)}}");
          background-repeat: no-repeat;
          background-size: 100% 100%;
          width: 1280px;
          height: 600px;
          display: inline-block;
          /*padding-top: 100px;*/

      }


      .centered {
          width: 100%;
          margin-top: -5em;
      }


      .centered .site_name {
          font-weight: bold;
          font-size: 1.2rem;
          color: #646464;
          padding-top: 165px;
          margin-bottom: 0em;
          margin-right: -10em;
      }

      .centered h1 {
          font-size: 1.6rem;
          color: #646464;
          padding-top: -1px;
          margin-bottom: 0em;
          margin-right: -8em;
      }

      .container h2 {
          float: left;
          font-size: 1.5rem;
          color: #646464;
          margin-right: -10em;
          padding-top: 115px;
      }

      .container h3 {
          margin-right: 5em;
          padding-top: 50px;
      }

      .centered p {
          font-size: 1rem;
          font-weight: bold;
          color: #525659;
          margin-top: 55px;
          margin-right: -9em;
      }

      .centered .signature {
          font-size: 1.3rem;
          color: #525659;
          padding-top: -45px;
          margin-right: -19.5em;
          font-style: italic;
      }
	</style>
</head>

<body dir="rtl">
<div class="container">
	<div class="centered">
		<div class="site_name">@lang("global.site_name")</div>
		<h1>{{$certificate->username}}</h1>
		<h2>{{$certificate->course->name}}</h2>
		<h3>{{$certificate->score}}%</h3>
		<p>{{ \Carbon\Carbon::parse($certificate->created_at)->format('Y/m/d') }}</p>
		<div class="signature">@lang("global.site_name")</div>
	</div>
</div>

</body>

</html>