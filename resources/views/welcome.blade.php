<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generate QR Code PDF</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            text-align: center;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            border: none;
            cursor: pointer;
        }

        .btn_logout {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2779bd;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
        }

        label {
            font-weight: 600;
        }

        input, button, select {
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        #response-box {
            display: none;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            background: #f0fdf4;
            border: 1px solid #34d399;
            margin-top: 20px;
        }

        #response-box h1 {
            color: #047857;
            font-size: 24px;
            margin-bottom: 10px;
        }

        #response-box i {
            color: #10b981;
            font-size: 40px;
        }

        #responseMessage {
            color: #065f46;
            font-size: 18px;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container" id="mainContainer">
        <h1>Generate PDF with QR Codes</h1>
        <p style="color: #ff0000;">Enter product type, page dimensions, QR code size, and row count.</p>

        <form id="qrForm">
            @csrf
            <label for="product_type">Select Product Type</label>
            <select id="product_type" name="product_type" required>
                <option value="" disabled selected>Select a product</option>
                <option value="w-beam">W-Beam</option>
                <option value="pole">Pole</option>
                <option value="high-mast">High Mast</option>
            </select>

            <label for="page_height">Page Height (mm)</label>
            <input type="text" id="page_height" name="page_height" required>

            <label for="page_width">Page Width (mm)</label>
            <input type="text" id="page_width" name="page_width" required>

            <label for="margin_top">Margin Top (mm)</label>
            <input type="text" id="margin_top" name="margin_top" required>

            <label for="margin_bottom">Margin Bottom (mm)</label>
            <input type="text" id="margin_bottom" name="margin_bottom" required>

            <label for="margin_left">Margin Left (mm)</label>
            <input type="text" id="margin_left" name="margin_left" required>

            <label for="margin_right">Margin Right (mm)</label>
            <input type="text" id="margin_right" name="margin_right" required>

            <label for="qr_height">QR Code Height (mm)</label>
            <input type="text" id="qr_height" name="qr_height" required>

            <label for="qr_width">QR Code Width (mm)</label>
            <input type="text" id="qr_width" name="qr_width" required>

            <label for="row_number">Number of QR</label>
            <input type="number" id="row_number" name="row_number" min="1" required>

            <button type="button" onclick="submitForm()">Generate PDF</button>
        </form>
    </div>

    <!-- Success Message -->
    <div id="response-box">
        <h1><i class="fa-solid fa-circle-check"></i></h1>
        <p id="responseMessage">PDF downloaded successfully!</p>
        <br>
        <a href="#" class="btn_logout">Logout</a>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (!localStorage.getItem('authToken')) {
                window.location.href = "/sangram/roygupta/143/sneider/qr"; // Redirect if token is missing
            }
        });

        document.querySelector(".btn_logout").addEventListener("click", function(event) {
            event.preventDefault(); 
            localStorage.removeItem("authToken"); 
            window.location.href = "/sangram/roygupta/143/sneider/qr"; 
        });

        function submitForm() {
            let formData = new FormData(document.getElementById("qrForm"));

            fetch("{{ url('/api/pages') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "Authorization": "Bearer " + localStorage.getItem('authToken')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log("API Response:", data); 
                    if (data.pdf_url) {
                        downloadFile(data.pdf_url, "data.pdf");

                        document.getElementById("mainContainer").style.display = "none";
                        document.getElementById("response-box").style.display = "block";
                        document.getElementById("responseMessage").innerText = "PDF successfully downloaded!";
                    } else {
                        alert("Error: No valid PDF URL received.");
                    }
                })
                .catch(error => {
                    alert("Error: " + error.message);
                });

            function downloadFile(url, filename) {
                let link = document.createElement("a");
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    </script>
</body>
</html>
