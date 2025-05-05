<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LandLink - Smart Industrial Park Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #000000;
            --secondary-dark: #454747;
            --accent-color: #4CAF50;
            --text-light: #fbfbfa;
            --text-muted: #bdbdbd;
            --card-bg: rgba(255, 255, 255, 0.1);
            --transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            --shadow-sm: 0 2px 5px rgba(0, 0, 0, 0.2);
            --shadow-md: 0 4px 10px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary-dark));
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            line-height: 1.6;
            background-attachment: fixed;
        }

        .container {
            max-width: 1200px;
            width: 90%;
            animation: fadeIn 1s ease-in-out;
            padding: 2rem;
        }

        h1 {
            font-size: clamp(3rem, 8vw, 5.5rem);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: var(--text-light);
            text-shadow: var(--shadow-md);
            margin-bottom: 1rem;
            background: linear-gradient(to right, #ffffff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2 {
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 400;
            margin: 1rem 0;
            color: rgba(255, 255, 255, 0.9);
        }

        .tagline {
            font-size: clamp(1rem, 2vw, 1.25rem);
            font-weight: 300;
            margin: 1.5rem auto;
            max-width: 800px;
            color: var(--text-muted);
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
            background-color: var(--card-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: var(--text-light);
            padding: 2.5rem 1.5rem;
            border-radius: 16px;
            width: 280px;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
        }

        .feature-box:hover {
            transform: translateY(-8px) scale(1.03);
            background-color: rgba(255, 255, 255, 0.95);
            color: var(--primary-dark);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .feature-box:hover h3,
        .feature-box:hover p {
            color: var(--primary-dark);
        }

        .feature-box h3 {
            font-size: 1.5rem;
            margin: 1.5rem 0 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .feature-box p {
            font-size: 1rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            transition: var(--transition);
        }

        button {
            background-color: var(--text-light);
            color: var(--primary-dark);
            font-size: 1.25rem;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 2rem;
            transition: var(--transition);
            font-weight: 600;
            letter-spacing: 1px;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        button:hover {
            background-color: var(--accent-color);
            color: var(--text-light);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        button:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            h1 {
                font-size: 2.5rem;
                letter-spacing: 2px;
            }
            
            .feature-box {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>LandLink</h1>
        <h2>Welcome to <span class="typedname"></span></h2>
        <p class="tagline">Effortlessly lease land, acquire top-quality raw materials, and sell your products in a seamless ecosystem.</p>

        <div class="features">
            <div class="feature-box">
                <h3><i class="bi bi-building"></i> Hassle-Free Land Leasing</h3>
                <p>Secure prime industrial land quickly and efficiently.</p>
            </div>
            <div class="feature-box">
                <h3><i class="bi bi-lightning-charge"></i> High-Quality Raw Materials</h3>
                <p>Get instant access to top-grade raw materials at the best prices.</p>
            </div>
            <div class="feature-box">
                <h3><i class="bi bi-cart3"></i> Sell Your Products</h3>
                <p>Showcase your industry's products to a wide network of buyers.</p>
            </div>
            <div class="feature-box">
                <h3><i class="bi bi-shield-check"></i> Transparent & Reliable</h3>
                <p>Enjoy a system built on trust, efficiency, and security.</p>
            </div>
        </div>

        <button onclick="window.location.href='home.php'">Get Started</button>
    </div>

    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <script>
        var typed = new Typed(".typedname", {
            strings: ["Industrial Park", "Smart Business Hub", "Future of Industry"],
            typeSpeed: 100,
            backSpeed: 80,
            loop: true,
            smartBackspace: true,
            showCursor: true,
            cursorChar: '|'
        });
    </script>

</body>
</html>