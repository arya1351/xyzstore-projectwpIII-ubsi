<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

 <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/icon_univ_bsi.png') }}">
 <title>tokoonline</title>

 <!-- Google font -->
 <link href="https://fonts.googleapis.com/css?family=Hind:400,700" rel="stylesheet">
 <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link
  href="https://fonts.googleapis.com/css2?family=Black+Ops+One&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap"
  rel="stylesheet">

 <!-- Bootstrap -->
 <link type="text/css" rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">

 <!-- Slick -->
 <link type="text/css" rel="stylesheet" href="{{ asset('frontend/css/slick.css') }}">
 <link type="text/css" rel="stylesheet" href="{{ asset('frontend/css/slick-theme.css') }}">

 <!-- nouislider -->
 <link type="text/css" rel="stylesheet" href="{{ asset('frontend/css/nouislider.min.css') }}">

 <!-- Font Awesome Icon -->
 <link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.min.css') }}">

 <!-- Custom stlylesheet -->
 <link type="text/css" rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">

 <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
 <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
 <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body>
 @if (session()->has('error'))
  <div id="floatingAlert" class="floating-alert alert alert-danger alert-dismissible fade in" role="alert">
   <button type="button" class="close" data-dismiss="alert">
    <span>&times;</span>
   </button>

   <strong>{{ session('error') }}</strong>
  </div>
 @endif

 <style>
  .floating-alert {
   position: fixed;
   right: 20px;
   z-index: 99999;
   margin-top: 2%;
   width: 350px;
   padding: 15px 45px 15px 15px;
   border-radius: 12px;
   box-shadow: 0 5px 20px rgba(0, 0, 0, 0.25);
   animation: slideIn 0.4s ease;
  }

  .floating-alert .close {
   position: absolute;
   top: 50%;
   right: 15px;
   transform: translateY(-50%);
   font-size: 22px;
  }

  @keyframes slideIn {
   from {
    opacity: 0;
    transform: translateX(100%);
   }

   to {
    opacity: 1;
    transform: translateX(0);
   }
  }

  @keyframes fadeOut {
   from {
    opacity: 1;
    transform: translateX(0);
   }

   to {
    opacity: 0;
    transform: translateX(100%);
   }
  }

  .hide-alert {
   animation: fadeOut 0.5s ease forwards;
  }
 </style>

 <script>
  setTimeout(function() {
   let alertBox = document.getElementById('floatingAlert');

   if (alertBox) {
    alertBox.classList.add('hide-alert');

    setTimeout(function() {
     alertBox.remove();
    }, 500);
   }
  }, 4000); // 3 detik
 </script>
 <!-- header -->
 <div id="header">
  <div class="container">
   <div class="pull-left">
    <!-- Logo -->
    <div class="header-logo">
     <a class="logo" href="/">
      <img src="{{ asset('./image/logo.png') }}" alt="">
     </a>
    </div>
    <!-- /Logo -->

    <!-- Search -->

    <!-- /Search -->
   </div>
   <div class="pull-right">
    <ul class="header-btns">
     <!-- Cart -->
     <li class="header-cart dropdown default-dropdown">
      <a class="dropdown-toggle" href="{{ route('order.cart') }}">
       <div class="header-btns-icon">
        <i class="fa fa-shopping-cart"></i>
       </div>
       <strong class="text-uppercase">Keranjang</strong>
      </a>
     </li>
     <!-- /Cart -->

     <!-- Account -->
     @if (Auth::check())
      <!-- Account -->
      <li class="header-account dropdown default-dropdown">
       <div class="dropdown-toggle" role="button" data-toggle="dropdown" aria-expanded="true">
        <div class="header-btns-icon">
         <i class="fa fa-user-o"></i>
        </div>
        <strong class="text-uppercase">{{ Auth::user()->nama }}<i class="fa fa-caret-down"></i></strong>
       </div>
       <ul class="custom-menu">
        <li><a href="{{ route('customer.akun', ['id' => Auth::user()->id]) }}"><i class="fa fa-user-o"></i> Akun
          Saya</a>
        </li>
        <li><a href="{{ route('order.history') }}"><i class="fa fa-check"></i> History</a></li>
        <li>
         <a href="#" onclick="event.preventDefault(); document.getElementById('keluar-app').submit();"><i
           class="fa fa-power-off"></i> Keluar
         </a>
         <!-- form keluar app -->
         <form id="keluar-app" action="{{ route('customer.logout') }}" method="POST" class="d-none">
          @csrf
         </form>
         <!-- form keluar app end -->
        </li>
       </ul>
      </li>
     @else
      <li class="header-account dropdown default-dropdown">
       <div class="dropdown-toggle" role="button" data-toggle="dropdown" aria-expanded="true">
        <div class="header-btns-icon">
         <i class="fa fa-user-o"></i>
        </div>
        <strong class="text-uppercase">Akun Saya<i class="fa fa-caret-down"></i></strong>
       </div>
       <a href="{{ route('auth.redirect') }}" class="text-uppercase">Login</a>
      </li>
      <!-- /Account -->
     @endif

     <!-- Mobile nav toggle-->
     <li class="nav-toggle">
      <button class="nav-toggle-btn main-btn icon-btn"><i class="fa fa-bars"></i></button>
     </li>
     <!-- / Mobile nav toggle -->
    </ul>
   </div>
  </div>
  <!-- header -->
 </div>
 <!-- container -->
 </header>
 <!-- /HEADER -->

 <!-- NAVIGATION -->
 <div id="navigation">
  <!-- container -->
  <div class="container">
   <div id="responsive-nav">
    @php
     $kategori = DB::table('kategori')->orderBy('nama_kategori', 'asc')->get();
    @endphp
    @if (request()->segment(1) == '' || request()->segment(1) == 'beranda')
     <!-- category nav -->
     <div class="category-nav">
      <span class="category-header">Kategori <i class="fa fa-list"></i></span>
      <ul class="category-list">
       @foreach ($kategori as $row)
        <li><a href="{{ route('produk.kategori', $row->id) }}">{{ $row->nama_kategori }}</a></li>
       @endforeach
      </ul>
      <ul class="category-list">
     </div>
    @else
     <div class="category-nav show-on-click">
      <span class="category-header">Kategori <i class="fa fa-list"></i></span>
      <ul class="category-list">
       @foreach ($kategori as $row)
        <li><a href="{{ route('produk.kategori', $row->id) }}">{{ $row->nama_kategori }}</a></li>
       @endforeach
      </ul>
     </div>
     <!-- /category nav -->
    @endif

    <!-- menu nav -->
    <div class="menu-nav">
     <span class="menu-header">Menu <i class="fa fa-bars"></i></span>
     <ul class="menu-list">
      <li><a href="{{ route('beranda') }}">Beranda</a></li>
      <li><a href="{{ route('produk.all') }}">Produk</a></li>
      <li><a href="{{ route('location') }}">Lokasi</a></li>
      <li><a href="https://wa.me/6282114943996">Hubungi Kami</a></li>
     </ul>
    </div>
    <!-- menu nav -->

   </div>
  </div>
  <!-- /container -->
 </div>
 <!-- /NAVIGATION -->
 @if (request()->segment(1) == '' || request()->segment(1) == 'beranda')
  <!-- HOME -->
  <div id="home">
   <!-- container -->
   <div class="container">
    <!-- home wrap -->
    <div class="home-wrap">
     <!-- home slick -->
     <div id="home-slick">
      <!-- banner -->
      <div class="banner banner-1">
       <img src="{{ asset('frontend/img/banner01.png') }}" alt="">
       <div class="banner-caption text-center">
        <h2 class="banner-title">
         Lagi cari <br> pakaian kece?
        </h2>
        <h3 class="font-weak banner-subtitle" style="color: 30323a;">XYZ Store solusinya</h3>
        <a href="{{ route('produk.all') }}" class="primary-btn banner-btn">Pesan Sekarang</a>
       </div>
      </div>

      <!-- /banner -->
      {{-- <!-- banner -->
      <div class="banner banner-1">
       <img src="{{ asset('frontend/img/banner02.jpg') }}" alt="">
       <div class="banner-caption">
        <h1 class="primary-color">Khas Makanan Indonesia<br><span class="white-color font-weak">Jajanan
          Tradisional</span></h1>
        <button class="primary-btn">Pesan Sekarang</button>
       </div>
      </div>
      <!-- /banner -->
      <!-- banner -->
      <div class="banner banner-1">
       <img src="{{ asset('frontend/img/banner03.jpg') }}" alt="">
       <div class="banner-caption">
        <h1 style="color: f8694a;">Khas Makanan <span>Indonesia</span></h1>
        <button class="primary-btn">Pesan Sekarang</button>
       </div>
      </div>
      <!-- /banner --> --}}
     </div>
     <!-- /home slick -->
    </div>
    <!-- /home wrap -->
   </div>
   <!-- /container -->
  </div>
  <!-- /HOME -->
 @endif
 <style>
  .banner-title {
   font-family: 'Black Ops One', system-ui;
   font-size: clamp(20px, 4vw, 42px);
   line-height: 1.2;
   color: #010101;
   margin-bottom: 10px;
  }

  .banner-subtitle {
   color: #30323a;
   font-size: clamp(12px, 2vw, 24px);
  }

     .banner-btn {
        border-radius: 6px;
   }

  @media (max-width: 768px) {
   .banner-caption {
    padding: 15px;
   }

   .banner-title {
    line-height: 1.2;
   }

   .banner-btn {
    padding: 10px 20px;
    font-size: 13px;
        border-radius: 6px;
   }
  }

  @media (max-width: 480px) {
   .banner-btn {
    padding: 8px 16px;
    font-size: 12px;
    border-radius: 6px;
   }

   
   .banner-title {
    line-height: 1.2;
       font-size: clamp(16px, 3vw, 36px);
   }
  }
   
  @media (max-width: 100px) {
   .banner-btn {
    padding: 6px 12px;
    font-size: 8px;
    border-radius: 6px;
   }
  }
 </style>
 <!-- section -->
 <div class="section">
  <!-- container -->
  <div class="container">
   <!-- row -->
   <div class="row">
    <!-- ASIDE -->
    <div id="aside" class="col-md-3">
     <!-- aside widget -->
     <div class="aside">
      <h3 class="aside-title">Produk Terbaru</h3>
      <!-- widget product -->
      @foreach ($newestproduct as $row)
       <div class="product product-widget">
        <div class="product-thumb">
         <img src="{{ asset('storage/img-produk/thumb_md_' . $row->foto) }}" alt="">
        </div>
        <div class="product-body">
         <h2 class="product-name"><a href="{{ route('produk.detail', $row->id) }}">{{ $row->nama_produk }}</a></h2>
         <h3 class="product-price">Rp. {{ number_format($row->harga, 0, ',', '.') }}</h3>

        </div>
       </div>
      @endforeach
      <!-- /widget product -->

     </div>
     <!-- /aside widget -->
     <!-- aside widget -->
     <div class="aside">
      <h3 class="aside-title">Filter Kategori</h3>
      <ul class="list-links">
       @foreach ($kategori as $row)
        <li><a href="{{ route('produk.kategori', $row->id) }}">{{ $row->nama_kategori }}</a></li>
       @endforeach
      </ul>
     </div>
     <!-- /aside widget -->

    </div>
    <!-- /ASIDE -->
    <!-- MAIN -->
    <div id="main" class="col-md-9">
     <!-- store top filter -->
     <!-- /store top filter -->

     <!-- @yieldAwal -->
     @yield('content')
     <!-- @yieldAkhir-->

     <!-- store bottom filter -->

     <!-- /store bottom filter -->
    </div>
    <!-- /MAIN -->
   </div>
   <!-- /row -->

  </div>
  <!-- /container -->
 </div>
 <!-- /section -->

 <!-- FOOTER -->
 <footer id="footer" class="section section-grey">
  <!-- container -->
  <div class="container">
   <!-- row -->
   <div class="row">
    <!-- footer widget -->
    <div class="col-md-3 col-sm-6 col-xs-6">
     <div class="footer">
      <!-- footer logo -->
      <div class="footer-logo">
       <a class="logo" href="#">
        <img src="./img/logo.png" alt="">
       </a>
      </div>
      <!-- /footer logo -->

      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore
       magna</p>

      <!-- footer social -->
      <ul class="footer-social">
       <li><a href="#"><i class="fa fa-facebook"></i></a></li>
       <li><a href="#"><i class="fa fa-twitter"></i></a></li>
       <li><a href="#"><i class="fa fa-instagram"></i></a></li>
       <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
       <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
      </ul>
      <!-- /footer social -->
     </div>
    </div>
    <!-- /footer widget -->
    @if (Auth::check())
     <!-- footer widget -->
     <div class="col-md-3 col-sm-6 col-xs-6">
      <div class="footer">
       <h3 class="footer-header">Akun Saya</h3>
       <ul class="list-links">
        <li><a href="{{ route('customer.akun', ['id' => Auth::user()->id]) }}">Detail Akun</a></li>
        <li><a href="{{ route('order.cart') }}">Keranjang Saya</a></li>
        <li><a href="{{ route('auth.redirect') }}">Login</a></li>
       </ul>
      </div>
     </div>
     <!-- /footer widget -->
    @else
     <!-- footer widget -->
     <div class="col-md-3 col-sm-6 col-xs-6">
      <div class="footer">
       <h3 class="footer-header">My Account</h3>
       <ul class="list-links">
        <li><a href="">My Account</a></li>
        <li><a href="">Keranjang Saya</a></li>
        <li><a href="">Login</a></li>
       </ul>
      </div>
     </div>
     <!-- /footer widget -->
    @endif

    <div class="clearfix visible-sm visible-xs"></div>

    <!-- footer widget -->
    <div class="col-md-3 col-sm-6 col-xs-6">
     <div class="footer">
      <h3 class="footer-header">Link</h3>
      <ul class="list-links">
       <li><a href="#">Beranda</a></li>
       <li><a href="#">Produk</a></li>
       <li><a href="#">About Us</a></li>
       <li><a href="#">Lokasi</a></li>
      </ul>
     </div>
    </div>
    <!-- /footer widget -->

    <!-- footer subscribe -->
    <div class="col-md-3 col-sm-6 col-xs-6">
     <div class="footer">
      <h3 class="footer-header">Stay Connected</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.</p>
      <form>
       <div class="form-group">
        <input class="input" placeholder="Enter Email Address">
       </div>
       <button class="primary-btn">Join Newslatter</button>
      </form>
     </div>
    </div>
    <!-- /footer subscribe -->
   </div>
   <!-- /row -->
   <hr>
   <!-- row -->
   <div class="row">
    <div class="col-md-8 col-md-offset-2 text-center">
     <!-- footer copyright -->
     <div class="footer-copyright">
      <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
      Copyright &copy;
      <script>
       document.write(new Date().getFullYear());
      </script> All rights reserved | This template is made with <i class="fa fa-heart-o"
       style="color: #a30669;" aria-hidden="true"></i>
      <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
     </div>
     <!-- /footer copyright -->
    </div>
   </div>
   <!-- /row -->
  </div>
  <!-- /container -->
 </footer>
 <!-- /FOOTER -->

 <!-- jQuery Plugins -->
 <script src="{{ asset('frontend/js/jquery.min.js') }}"></script>
 <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
 <script src="{{ asset('frontend/js/slick.min.js') }}"></script>
 <script src="{{ asset('frontend/js/nouislider.min.js') }}"></script>
 <script src="{{ asset('frontend/js/jquery.zoom.min.js') }}"></script>
 <script src="{{ asset('frontend/js/main.js') }}"></script>

</body>

</html>
