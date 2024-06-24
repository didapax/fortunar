<!DOCTYPE html>
<html lang="es" style=" overflow: hidden;">
<head>
  <title>Fortuna Royal</title>
  <link rel="manifest" href="manifest.json">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0" />
  <link rel="shortcut icon" href="Feed/Home/favicon.png">
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet' href='https://netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.css'>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>
  <script type="text/javascript" src="lib/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="lib/tipTipv13/jquery.tipTip.minified.js"></script>
</head>
  <header>
    <script>

    </script>
  </header>
  <body style="min-height: 100vh; margin:0 auto; width: 100%;">
    <div style="width: 100%; height:90%; height: absolute; width: absolute; min-height: 90vh; margin:0px; calc(100% - 7em);" class="frame">
      <iframe id="dynamic-iframe" style="position:0; top:0; left:0; bottom:0; right:0; width:100%; height:100%;
       border:none; margin:0; padding:0; overflow:hidden;
       z-index:-1;" src="Feed/Home/home.php" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
      </iframe>
    </div>
  <menu class="menu">
      <button class="change-iframe active" data-src="Feed/Home/home.php" style="--bgColorItem: #ff8c00;">
          <a href="#dashboard">
            <div>
              <h1 style="color:powderblue;
              text-align: center"</h1>
            <span  class="icon icon-home"></span>
          </div>
          </a>
      </button>

      <button class="change-iframe" data-src="Feed/Games/games.php" style="--bgColorItem: #f54888;">
          <a href="#dashboard">
            <div>
              <h1 style="color:powderblue;
              text-align: center"</h1>
            <span  class="icon icon-gamepad"></span>
          </div>
          </a>
      </button>

      <button class="change-iframe" data-src="Feed/Trade/trade.php?token=FRT" style="--bgColorItem: #4343f5;" >
          <a href="#dashboard" accesskey="1">
            <div>
              <h1 style="color:powderblue;
              text-align: center"</h1>
            <span  class="icon icon-bar-chart"></span>
          </div>
          </a>
      </button>

      <button class="change-iframe" data-src="Feed/Credit/credit.php" style="--bgColorItem: #e0b115;" >
          <a href="#dashboard" accesskey="1">
            <div>
               <h1 style="color:powderblue;
               text-align: center"</h1>
            <span  class="icon icon-credit-card"></span>
          </div>
          </a>
      </button>

      <button class="change-iframe" data-src="Feed/Book/book.php" style="--bgColorItem:#65ddb7;">
          <a href="#dashboard" accesskey="1">
            <div>
              <h1 style="color:powderblue;
              text-align: center"</h1>
            <span  class="icon icon-book"></span>
          </div>
          </a>
      </button>

      <div class="menu__border"></div>

    </menu>

    <div class="svg-container">
      <svg viewBox="0 0 202.9 45.5" >
        <clipPath id="menu" clipPathUnits="objectBoundingBox" transform="scale(0.0049285362247413 0.021978021978022)">
          <path  d="M6.7,45.5c5.7,0.1,14.1-0.4,23.3-4c5.7-2.3,9.9-5,18.1-10.5c10.7-7.1,11.8-9.2,20.6-14.3c5-2.9,9.2-5.2,15.2-7
            c7.1-2.1,13.3-2.3,17.6-2.1c4.2-0.2,10.5,0.1,17.6,2.1c6.1,1.8,10.2,4.1,15.2,7c8.8,5,9.9,7.1,20.6,14.3c8.3,5.5,12.4,8.2,18.1,10.5
            c9.2,3.6,17.6,4.2,23.3,4H6.7z"/>
        </clipPath>
      </svg>
    </div>
  <!-- partial -->
    <script  src="./script.js"></script>
    <script src="./page_script.js" defer></script>
  </div>

  <footer>
    <div class="footer">
      <div>
        <img src="dark_bar.png" style="position: fixed; left: 0%; bottom:0%; width: 200%; height: absolute; max-height: 130px; min-height: 50px;">
      </div>
    </div>
  </footer>
<!-- partial:index.partial.html -->

<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script><script  src="scr_feed.js"></script>
</body>
</html>
