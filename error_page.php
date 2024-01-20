<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mobile Only Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f8f9fa;
    }

    .container {
      text-align: center;
      margin-top: 0px;
      border: 4px solid #f9c918;
      padding: 50px 25px;
      height: 60rem;
    }

    .message {
      font-size: 26px;
      margin-top: 30px;
      color: #bc1823;
      font-weight: bold;
    }

    .responsive-img {
      max-width: 50%;
      margin-top: 50%;
      height: auto;
    }
  </style>
</head>
<body>

<div class="container">
  <img src="images/logo.png" alt="Responsive Image" class="img-fluid responsive-img">
  <center>
  <div class="message">
    This page is only available for mobile devices.
  </div>
  </center>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // JavaScript to check if the device is a mobile device
  window.onload = function() {
    if (/Mobi|Android/i.test(navigator.userAgent)) {
      document.querySelector('.message').innerText = 'This site is accessible exclusively on laptop or desktop devices. We appreciate your cooperation.'
    } else {
      document.querySelector('.message').innerText = 'This page is only available for mobile devices. Please visit from a mobile device.';
    }
  };
</script>

</body>
</html>
