<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title')</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>

    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="#" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block">WaterGo</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>
        @include('layouts.partials.header')
    </header>

    <aside id="sidebar" class="sidebar"><ul class="sidebar-nav" id="sidebar-nav">@include('layouts.partials.menu')</ul></aside>

    <main id="main" class="main">@yield('content')</main>
    
    <footer id="footer" class="footer">@include('layouts.partials.footer')</footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script>
        $(".phone").inputmask("+998 99 999 9999");
        $(".guvoxnoma_serya").inputmask("AAAA");
        $(".guvoxnoma_raqam").inputmask("999999999");
        $(".passport").inputmask("AA 9999999");
        $(".work_time").inputmask("99:99 - 99:99");
        $(".inn").inputmask("9999999999");
        $(".long_lat").inputmask("99.9999999");
        $("#amount").inputmask({
            alias: "numeric",
            groupSeparator: " ",     // Xona birliklarini probel bilan ajratadi
            digits: 0,               // Verguldan keyingi raqamlar (kerak bo'lsa 2 qiling)
            autoGroup: true,         // Avtomatik guruhlash
            rightAlign: false,       // Matnni chapdan boshlab yozish
            removeMaskOnSubmit: true // Formani yuborganda probellarni olib tashlaydi
        });
        $("#amount2").inputmask({
            alias: "numeric",
            groupSeparator: " ",     // Xona birliklarini probel bilan ajratadi
            digits: 0,               // Verguldan keyingi raqamlar (kerak bo'lsa 2 qiling)
            autoGroup: true,         // Avtomatik guruhlash
            rightAlign: false,       // Matnni chapdan boshlab yozish
            removeMaskOnSubmit: true // Formani yuborganda probellarni olib tashlaydi
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</body>

</html>