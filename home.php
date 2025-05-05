<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LandLink - Smart Industrial Park Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #e74c3c;
            --light-color: #ffffff;
            --dark-color: #1a1a1a;
            --text-color: #333333;
            --text-light: #7f8c8d;
            --transition: all 0.3s ease;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: rgba(255, 255, 255, 0.98);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin-top: 100px;
            animation: fadeIn 1s ease-in-out;
            padding: 0 20px;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 400;
            color: var(--text-light);
            margin-bottom: 2rem;
            text-align: center;
        }

        .tagline {
            font-size: 1.25rem;
            color: var(--text-color);
            max-width: 800px;
            margin: 0 auto 3rem;
            line-height: 1.8;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature-box {
            background: var(--light-color);
            color: var(--text-color);
            padding: 2rem;
            border-radius: 12px;
            width: 280px;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }

        .feature-box h3 {
            font-size: 1.5rem;
            margin: 1.5rem 0 1rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .feature-box p {
            font-size: 1rem;
            color: var(--text-light);
            margin-top: 0.5rem;
        }

        button {
            background-color: var(--primary-color);
            color: var(--light-color);
            font-size: 1.1rem;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 2rem;
            transition: var(--transition);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        footer {
            background-color: var(--dark-color);
            color: var(--light-color);
            text-align: center;
            padding: 1rem 0;
            width: 100%;
            margin-top: auto;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar {
            width: 100%;
            background-color: var(--dark-color);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0.8rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: var(--light-color);
            text-decoration: none;
            padding: 0.7rem 1.2rem;
            font-size: 1rem;
            font-weight: 500;
            transition: var(--transition);
            border-radius: 5px;
            margin: 0 0.2rem;
        }

        .navbar a:hover {
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: var(--light-color);
            min-width: 200px;
            box-shadow: var(--shadow);
            border-radius: 8px;
            z-index: 1;
            overflow: hidden;
            top: 100%;
            left: 0;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: var(--text-color);
            padding: 0.8rem 1rem;
            text-decoration: none;
            display: block;
            text-align: left;
            transition: var(--transition);
        }

        .dropdown-content a:hover {
            background-color: #f8f9fa;
        }

        .navbar .cname {
            font-size: 1.4rem;
            font-weight: 600;
            margin-right: auto;
            color: var(--light-color);
        }

        .navbar .cname:hover {
            background-color: transparent;
        }

        .active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.3em;
            vertical-align: 0.15em;
            content: "";
            border-top: 0.35em solid;
            border-right: 0.35em solid transparent;
            border-bottom: 0;
            border-left: 0.35em solid transparent;
        }

        @media (max-width: 992px) {
            .navbar {
                flex-wrap: wrap;
                padding: 0.8rem;
            }
            
            .navbar a {
                padding: 0.5rem 0.8rem;
                font-size: 0.9rem;
            }
            
            h1 {
                font-size: 2.5rem;
            }
            
            .feature-box {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <a class="cname" href="index.php">LandLink</a>
    <a class="active" href="home.php">Home</a>
    <a href="login.html">Login</a>

    <div class="dropdown">
        <a href="#">Industry <span class="dropdown-toggle"></span></a>
        <div class="dropdown-content">
            <a href="industry/checkindustry.php">Check Approval Status</a>
            <a href="industry/reg.php">Register</a>
        </div>
    </div>

    <div class="dropdown">
        <a href="#">Client <span class="dropdown-toggle"></span></a>
        <div class="dropdown-content">
            <a href="client/clientreg.php">Register</a>
            <a href="client/checkclient.php">Check Approval Status</a>
        </div>
    </div>

    <a href="industry/industry.php">Existing Industry</a>
    <a href="viewproducts.php">Products</a>
    <a href="landview.php">Plot</a>
    <a href="about.php">About Us</a>
</div>

<!-- Main Content -->
<div class="container">
    <h1>Welcome to LandLink</h1>
    <h2>The Future of Smart Industrial Park Management</h2>
    <p class="tagline">
        Looking for a seamless and efficient way to establish and grow your industry?  
        LandLink simplifies <strong>land leasing, raw material procurement, and product sales  
        all in one powerful platform</strong>.  
    </p>

    <div class="features">
        <div class="feature-box">
            <h3><i class="bi bi-building"></i> Hassle-Free Land Leasing</h3>
            <p>Secure prime industrial land quickly and efficiently with our transparent leasing process.</p>
        </div>
        <div class="feature-box">
            <h3><i class="bi bi-box-seam"></i> High-Quality Raw Materials</h3>
            <p>Get instant access to top-grade raw materials at competitive market prices.</p>
        </div>
        <div class="feature-box">
            <h3><i class="bi bi-cart"></i> Sell Your Products</h3>
            <p>Showcase your industry's products to a wide network of potential buyers.</p>
        </div>
        <div class="feature-box">
            <h3><i class="bi bi-shield-check"></i> Transparent & Reliable</h3>
            <p>Enjoy a system built on trust, efficiency, and enterprise-grade security.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>    
</body>
</html>