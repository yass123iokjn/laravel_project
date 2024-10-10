<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap");

        .developer-image {
            position: absolute;
            right: 1500px; /* Adjust positioning */
            top: 50%; /* Center vertically */
            transform: translateY(-50%); /* Center vertically */
            width: 450px; /* Adjust the size of the image */
            animation: slide-in 1s ease-out; /* Animation for image */
        }
        .already-registered {
            float: right;
            font-size: 0.9rem;
            margin-top: 1rem;
            color: #fff;
            animation: slide-down 1s ease-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body,
        input,
        textarea {
            font-family: "Poppins", sans-serif;
        }

        .container {
            position: relative;
            width: 100%;
            min-height: 100vh;
            padding: 2rem;
            background-color: #fafafa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .form {
            width: 100%;
            max-width: 820px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px 1px rgba(0, 0, 0, 0.1);
            z-index: 1000;

            display: grid;
            grid-template-columns: 1fr;
            overflow: hidden;
        }

        .contact-form {
            background-color: #1abc9c;
            position: relative;
        }

        /* Circles styling */
        .circle {
            border-radius: 50%;
            background: linear-gradient(135deg, transparent 20%, #149279);
            position: absolute;
        }

        .circleone {
            width: 130px;
            height: 130px;
            top: 20px;
            left: 50%; /* Center horizontally */
            transform: translateX(-50%);
        }

        .circletwo {
            width: 80px;
            height: 80px;
            top: 50px;
            right: 50%; /* Center horizontally */
            transform: translate(50%, 0);
        }

        .contact-form:before {
            content: "";
            position: absolute;
            width: 26px;
            height: 26px;
            background-color: #1abc9c;
            transform: rotate(45deg);
            top: 50px;
            left: -13px;
        }

        form {
            padding: 2.3rem 2.2rem;
            z-index: 10;
            position: relative;
        }

        .title {
            color: #fff;
            font-weight: 500;
            font-size: 1.5rem;
            margin-bottom: 0.7rem;
        }

        .input-container {
            position: relative;
            margin: 1rem 0;
        }

        .input {
            width: 100%;
            outline: none;
            border: 2px solid #fafafa;
            background: none;
            padding: 0.6rem 1.2rem;
            color: #fff;
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 25px;
            transition: 0.3s;
        }

        .input-container label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            padding: 0 0.4rem;
            color: #fafafa;
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 1000;
            transition: 0.5s;
        }

        .btn {
            padding: 0.6rem 1.3rem;
            background-color: #fff;
            border: 2px solid #fafafa;
            font-size: 0.95rem;
            color: #1abc9c;
            line-height: 1;
            border-radius: 25px;
            outline: none;
            cursor: pointer;
            transition: 0.3s;
            margin: 0;
        }

        .btn:hover {
            background-color: transparent;
            color: #fff;
        }

        .big-circle {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(to bottom, #1cd4af, #159b80);
            bottom: 60%;
            right: 70%;
            transform: translate(-40%, 38%);
        }

        .big-circle1 {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(to bottom, #1cd4af, #159b80);
            bottom: 60%;
            right: -100%;
            transform: translate(-50%, 38%);
        }

        .big-circle3 {
            position: absolute;
            width: 900px;
            height: 900px;
            border-radius: 50%;
            background: linear-gradient(to bottom, #1cd4af, #159b80);
            bottom: 60%;
            right: -60%;
            transform: translate(-80%, 38%);
        }

        .big-circle2 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(to bottom, #1cd4af, #159b80);
            bottom: 10%;
            right: 70%;
            transform: translate(-80%, 38%);
        }

        .square {
            position: absolute;
            height: 400px;
            top: 50%;
            left: 50%;
            transform: translate(181%, 11%);
            opacity: 0.2;
        }

        @media (max-width: 768px) {
            .form {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .circleone,
            .circletwo {
                width: 80px; /* Adjusted size for smaller screens */
                height: 80px;
            }

            .big-circle {
                width: 300px;
                height: 300px;
                bottom: 50%;
                right: 50%;
                transform: translate(50%, 50%);
            }

            .title {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .input {
                font-size: 0.9rem; /* Smaller input font size */
            }

            .btn {
                font-size: 0.85rem; /* Smaller button font size */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <span class="big-circle"></span>
        <span class="big-circle1"></span>
        <span class="big-circle2"></span>
        <span class="big-circle3"></span>
        <span class="circleone"></span>
        <span class="circletwo"></span>

        <img src="{{ asset('img/shape.png') }}" class="square" alt="Shape">
        <div class="form">
            <div class="contact-info">
                <div class="contact-form">
                    <form method="POST" action="{{ route('register') }}" autocomplete="off">
                        @csrf
                        <h3 class="title">Register</h3>

                        <!-- Name -->
                        <div class="input-container">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="input-container">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="input" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="input-container">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="input" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-container">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="input" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="input-container">
    <button class="btn" type="submit">Register</button>

    
    <a class="already-registered underline text-sm rounded-md" href="{{ route('login') }}">
                                {{ __('Already registered?') }}
                            </a>
    
</div>

                    </form>
                </div>
            </div>
            <img src="{{ asset('images/undraw_developer_activity_re_39tg.svg') }}" class="developer-image" alt="Developer Image">
        </div>
    </div>
</body>

</html>
