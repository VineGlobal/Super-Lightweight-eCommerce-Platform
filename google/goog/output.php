<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>enarion.net demo page <?= $output_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="_res/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px;
      }
    </style>
    <link href="_res/css/bootstrap-responsive.css" rel="stylesheet">
  </head>

  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <div class="nav-collapse">
            <ul class="nav">
              <?php echo $output_nav ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
		<?php echo $output_body ?>
    </div>

  </body>
</html>
