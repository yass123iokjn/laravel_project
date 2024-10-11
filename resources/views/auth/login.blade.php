<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- Use your local CSS file -->
    <script src="https://kit.fontawesome.com/a81368914c.js"></script> <!-- Font Awesome for icons -->
    <script>
        // Function to show and hide the alert
        function showAlert() {
            const alert = document.getElementById('error-alert');
            if (alert) {
                alert.classList.remove('hidden');
                setTimeout(() => {
                    alert.classList.add('hidden');
                }, 10); // Change 3000 to the number of milliseconds you want the alert to stay visible
            }
        }
    </script>
</head>

<body class="overflow-hidden" style="background-color: #eafaf1;">
    <img class="wave animate-wave" src="{{ asset('images/wave.png') }}" alt="Wave Background">
    <img class="wave-top-right" src="{{ asset('images/wave.png') }}" alt="Top Right Wave">

    <div class="container mx-auto flex items-center justify-center min-h-screen">
        <div class="img">
            <img src="{{ asset('images/undraw_mobile_login_re_9ntv.svg') }}" alt="Login Illustration" class="w-96 transition-transform duration-700 animate-slide-in">
        </div>
        <div class="login-content bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <form method="POST" action="{{ route('login') }}" onload="showAlert()">
                @csrf <!-- CSRF Token for security -->

                <!-- Error alert -->
                @if (session('error'))
                    <div id="error-alert" class="bg-white text-red-600 border border-red-600 p-4 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <img src="{{ asset('images/avatar.svg') }}" alt="Avatar" class="h-24 mx-auto mb-4">
                <h2 class="title text-2xl font-bold text-center text-gray-700">Welcome</h2>

                <!-- Email Address -->
                <div class="input-div one mt-6 focus-within">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <h5>Email</h5>
                        <input id="email" class="input border-b border-gray-300 focus:border-green-500 outline-none transition duration-300" type="email" name="email" required autofocus autocomplete="email">
                    </div>
                </div>

                <!-- Password -->
                <div class="input-div pass mt-4 focus-within">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Password</h5>
                        <input id="password" class="input border-b border-gray-300 focus:border-green-500 outline-none transition duration-300" type="password" name="password" required autocomplete="current-password">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex justify-between items-center mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-green-500">Forgot Password?</a>
                </div>

                <input type="submit" class="btn bg-gradient-to-r from-green-400 to-green-500 text-white py-2 rounded-full mt-4 w-full cursor-pointer transition duration-300 hover:scale-105" value="Login">
            </form>
        </div>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>

    <style>
        /* CSS Styles */
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .wave {
            position: fixed;
            bottom: 0;
            left: 0;
            height: 100%;
            z-index: -1;
        }

        .wave-top-right {
            position: fixed;
            top: 0;
            right: 0;
            height: 50%;
            z-index: -1;
            transform: rotate(180deg);
        }

        .container {
            width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 7rem;
            padding: 0 2rem;
        }

        .img {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .login-content {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
        }

        .img img {
            width: 400px;
        }

        form {
            width: 360px;
        }

        .login-content img {
            height: 100px;
        }

        .login-content h2 {
            margin: 15px 0;
            color: #333;
            text-transform: uppercase;
            font-size: 2.9rem;
        }

        .login-content .input-div {
            position: relative;
            display: grid;
            grid-template-columns: 7% 93%;
            margin: 25px 0;
            padding: 5px 0;
            border-bottom: 2px solid #d9d9d9;
        }

        .login-content .input-div.one {
            margin-top: 0;
        }

        .i {
            color: #d9d9d9;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .i i {
            transition: .3s;
        }

        .input-div > div {
            position: relative;
            height: 45px;
        }

        .input-div > div > h5 {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            transition: .3s;
        }

        .input-div:before,
        .input-div:after {
            content: '';
            position: absolute;
            bottom: -2px;
            width: 0%;
            height: 2px;
            background-color: #38d39f;
            transition: .4s;
        }

        .input-div:before {
            right: 50%;
        }

        .input-div:after {
            left: 50%;
        }

        .input-div.focus:before,
        .input-div.focus:after,
        .input-div:focus-within:before,
        .input-div:focus-within:after {
            width: 50%;
        }

        .input-div.focus > div > h5,
        .input-div:focus-within h5 {
            top: -5px;
            font-size: 15px;
            color: #38d39f;
        }

        .input-div.focus > .i > i,
        .input-div:focus-within .i > i {
            color: #38d39f;
        }

        .input-div > div > input {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            background: none;
            padding: 0.5rem 0.7rem;
            font-size: 1.2rem;
            color: #555;
            font-family: 'Poppins', sans-serif;
        }

        .input-div.pass {
            margin-bottom: 4px;
        }

        a {
            display: block;
            text-align: right;
            text-decoration: none;
            color: #999;
            font-size: 0.9rem;
            transition: .3s;
        }

        a:hover {
            color: #38d39f;
        }

        .btn {
            background: linear-gradient(to right, #38d39f, #30d84a);
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 50px;
            color: #fff;
            font-size: 1rem;
            transition: .3s;
        }

        .btn:hover {
            opacity: .8;
        }

        @keyframes slide-in {
            0% {
                transform: translateX(-50%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.5s ease-out forwards;
        }

        @keyframes wave-animation {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-20%);
            }
        }

        .animate-wave {
            animation: wave-animation 10s linear infinite;
        }
    </style>
</body>

</html>
