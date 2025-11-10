<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../ClassAutoLoad.php';

$components = new Components(); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <title>Start Selling on StrathMart</title>
    
    <style>
       
        body {
            background-color: #f8f9fa; 
        }

      
        .hero-section {
            background-image: url('images/image.png'); 
            background-size: cover;
            background-position: center;
            height: 70vh; 
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.55); 
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 700px;
            padding: 0 15px;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .hero-content p {
            font-size: 1.3rem;
            font-weight: 300;
        }

      
        .call-to-action-card {
            background-color: white;
            color: #333;
            padding: 40px 20px;
            margin-top: -100px; 
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); 
            position: relative;
            z-index: 3; 
        }

        .call-to-action-card h2 {
            font-weight: 400; 
            font-size: 1.75rem;
        }

        .call-to-action-card .btn:nth-child(1) {
            background-color: #3f51b5 !important; 
            border-color: #3f51b5 !important;
            color: white !important;
            padding: 12px 40px !important; 
            font-size: 1.1rem !important;
            font-weight: 500;
        }
        .call-to-action-card .btn:nth-child(1):hover {
             background-color: #303f9f !important;
             border-color: #303f9f !important;
        }

        .call-to-action-card .btn:nth-child(2) {
            color: #3f51b5 !important;
            background-color: transparent !important;
            border: 2px solid #3f51b5 !important; 
            padding: 12px 40px !important;
            font-size: 1.1rem !important;
            font-weight: 500;
        }
        .call-to-action-card .btn:nth-child(2):hover {
            background-color: #3f51b5 !important;
            color: white !important;
        }

        .footer-custom {
            background-color: #212529; 
            color: #ccc;
            padding: 30px 0;
            font-size: 0.9rem;
        }

        .footer-custom h5, .footer-custom p {
            color: white;
        }

        .footer-custom a {
            color: #ccc;
            text-decoration: none;
        }
        .footer-custom a:hover {
            color: #fff;
        }
        .social-icon-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<?php

$components->header(); 
?>

<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="display-5">Start Selling on Campus</h1>
        <p>Join other students making money from items</p>
    </div>
</div>

<div class="container d-flex justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="call-to-action-card text-center">
            <h2 class="mb-4">Ready to Start Selling?</h2>
            <p class="lead mb-5">Create your free StrathMart Account Today!</p>
            
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                
                <a href="/../Iap-group-project/index.php" class="btn btn-lg"> 
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-in-right me-2" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                      <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708L8.207 4.5a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.794 2.793a.5.5 0 0 0 .708.708z"/>
                    </svg>
                    Login to Your Account
                </a>
                
                <a href="/../Iap-group-project/index.php" class="btn btn-lg"> 
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-plus me-2" viewBox="0 0 16 16">
                      <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                      <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                    Create New Account
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container my-5 py-5">
    </div>

<footer class="footer-custom">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">StrathMart</h5>
                <p>The trusted Marketplace for all Stratizens to quickly and conveniently buy and sell products within campus.</p>
            </div>

            <div class="col-md-3 mb-4 mb-md-0">
                <h5 class="text-uppercase">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="buy.php">Buy</a></li>
                    <li><a href="sell.php">Sell</a></li>
                    <li><a href="about.php">About Us</a></li>
                </ul>
            </div>

            <div class="col-md-3 text-md-end d-flex align-items-center justify-content-start justify-content-md-end">
                <div class="social-icon-circle"></div>
                <div class="social-icon-circle"></div>
                <div class="social-icon-circle"></div>
                <div class="social-icon-circle"></div>
            </div>
        </div>
        <div class="row mt-3">
             <div class="col text-center">
                 <p class="text-muted small">&copy; <?php echo date("Y"); ?> StrathMart. All Rights Reserved.</p>
             </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>