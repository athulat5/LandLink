<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | LandLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --text-color: #333;
            --light-color: #fff;
            --dark-color: #1a1a1a;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
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

        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            background-color: var(--light-color);
            color: var(--text-color);
            line-height: 1.8;
            padding-top: 80px;
        }

        .about-section {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            text-align: center;
            font-size: 2.8rem;
            margin-bottom: 2rem;
            color: var(--primary-color);
            position: relative;
            padding-bottom: 1rem;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--accent-color);
        }

        h2 {
            font-size: 1.8rem;
            margin: 3rem 0 1.5rem;
            color: var(--primary-color);
            position: relative;
            padding-left: 1rem;
        }

        h2::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background: var(--accent-color);
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 1s ease-in-out;
        }

        .btn {
            display: inline-block;
            text-align: center;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background-color: var(--primary-color);
            color: var(--light-color);
            text-decoration: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border: 2px solid var(--primary-color);
        }

        .btn:hover {
            background-color: transparent;
            color: var(--primary-color);
            transform: translateY(-3px);
        }

        footer {
            text-align: center;
            background: var(--dark-color);
            color: var(--light-color);
            padding: 1.5rem;
            margin-top: 3rem;
        }

        .navbar {
            width: 100%;
            background-color: var(--dark-color);
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 1rem;
            padding-bottom: 1rem;
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
            font-size: 0.95rem;
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
            font-size: 1.3rem;
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

        @media (max-width: 768px) {
            .navbar {
                flex-wrap: wrap;
                padding: 0.8rem;
            }
            
            .navbar a {
                padding: 0.5rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .about-section {
                padding: 2rem 1.5rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a class="cname" href="home.php">LandLink</a>
        <a href="home.php">Home</a>
        <a href="login.html">Login</a>

       

        <a href="#">Existing Industry</a>
        <a href="#">Products</a>
        <a class="active" href="about.php">About Us</a>
    </div>

    <section class="about-section">
        <h1>About LandLink</h1>
        <p>
            <strong>LandLink</strong> is a next-generation industrial platform designed to revolutionize the way businesses acquire land, manage resources, and trade products. 
            We provide a seamless and secure digital space where industries can grow without limitations. Our mission is to streamline industrial operations by offering a transparent 
            and efficient system that connects businesses with the right resources.
        </p>

        <h2>What We Offer</h2>
        <p>
            LandLink simplifies the process of land leasing and registration for industries, ensuring a smooth approval system that eliminates unnecessary delays. 
            Businesses can purchase raw materials from verified suppliers and sell their products in a robust marketplace designed for growth. Our platform also provides 
            a dedicated dashboard for industries, clients, and staff, offering a user-friendly interface for managing operations effectively.
        </p>

        <h2>Our Vision</h2>
        <p>
            At LandLink, we envision a future where industrial development is streamlined, eco-friendly, and accessible to all businesses. 
            Our goal is to create a digitally powered industrial ecosystem that fosters growth, collaboration, and efficiency. 
            By embracing technology and innovation, we aim to empower industries and contribute to a more sustainable and productive economy.
        </p>

        <h2>Why Choose LandLink?</h2>
        <p>
            Our platform is designed to be user-friendly, offering a straightforward and transparent experience for all users. 
            We ensure 100% verified industries and clients, providing a secure business environment with no hidden costs. 
            Our automated approval system speeds up processing times, and our 24/7 customer support ensures that your business needs are always met.
        </p>

        <h2>Our Impact</h2>
        <p>
            LandLink is transforming the industrial landscape by making land acquisition, resource procurement, and product sales more accessible and efficient. 
            We have helped numerous industries establish themselves with ease, reducing costs and increasing productivity. 
            Our commitment to innovation and customer satisfaction continues to drive us toward making industrial growth a seamless experience for all.
        </p>
    </section>

    <footer>
        <p>&copy; 2025 LandLink. All Rights Reserved.</p>
    </footer>
</body>
</html>