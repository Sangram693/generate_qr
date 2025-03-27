<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Login Page</title>

    <!-- Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f3f4f6;
        }
        .container {
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            background: #fff;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-weight: 600;
            margin-top: 10px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #3490dc;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #2779bd;
        }
        .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
        .success {
            color: green;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        
        <form id="loginForm">
            @csrf
            <label for="email">User Name</label>
            <input type="text" id="email" name="email" placeholder="Enter your user name" required>
            
            <label for="password">Password</label>
            <input type="text" id="password" name="password" placeholder="Enter your password" required>
            
            <button type="button" onclick="submitLogin()">Login</button>
        </form>

        <p id="responseMessage" class="error"></p>
    </div>

    <!-- JavaScript for form submission and token handling -->
    <script>
        function submitLogin() {
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const responseMessage = document.getElementById("responseMessage");

            fetch("{{ url('/api/login') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    user_name: email,
                    password: password,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    // Store the token dynamically
                    localStorage.setItem("authToken", data.token);
                    responseMessage.className = "success";
                    responseMessage.innerText = "Login successful!";
                    
                    // Redirect to the QR Code generation page
                    setTimeout(() => {
                        window.location.href = "{{ url('/generate') }}";
                    }, 1000);
                } else {
                    responseMessage.className = "error";
                    responseMessage.innerText = "Invalid email or password.";
                }
            })
            .catch(error => {
                responseMessage.className = "error";
                responseMessage.innerText = "Error: " + error.message;
            });
        }
    </script>
</body>
</html>
