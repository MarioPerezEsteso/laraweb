<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Geeky Theory Cursos</title>
    <meta name="description" content="Geeky Theory Cursos"/>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Loading Bootstrap -->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">

    <!-- Loading Flat UI -->
    <link href="assets/vendor/flat-ui/dist/css/flat-ui.css" rel="stylesheet">
    <link href="{{ autoVersion('assets/courses/css/app.css') }}" rel="stylesheet">

    <link rel="shortcut icon" href="assets/vendor/flat-ui/img/favicon.ico">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
    <script src="assets/vendor/flat-ui/dist/js/vendor/html5shiv.js"></script>
    <script src="assets/vendor/flat-ui/dist/js/vendor/respond.min.js"></script>
    <![endif]-->

    <link href='https://fonts.googleapis.com/css?family=Raleway:400' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>

    @include('courses.partials.cookies')
</head>
<body>

@include('courses.partials.navbar')

<section class="container-fluid section-pricing section-pricing-mt-53">
    @include('courses.partials.pricing')
</section>

@include('courses.partials.footer')

<script src="assets/vendor/flat-ui/dist/js/vendor/jquery.min.js"></script>
<script src="assets/vendor/flat-ui/dist/js/vendor/video.js"></script>
<script src="assets/vendor/flat-ui/dist/js/flat-ui.min.js"></script>
<script src="assets/vendor/flat-ui/docs/assets/js/application.js"></script>

</body>
</html>
