<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Generate QR Code PDF</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            text-align: center;
            margin: 50px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            background: #f9f9f9;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate PDF with QR Codes</h1>
        <p style="color: #ff0000">Enter product type, page dimensions, QR code size, and row count.</p>
        
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

        <p id="responseMessage" style="color: red; font-weight: bold;"></p>
    </div>

    <!-- JavaScript for handling form submission -->
    <script>
        function submitForm() {
            let formData = new FormData(document.getElementById("qrForm"));

            fetch("{{ url('/api/pages') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "Authorization": "Bearer 144|9iB2Wf9YbfcoIeP10OTEghGcKURHtAATEE9bhzX8390f651b"
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Failed to generate PDF");
                }
                return response.blob();
            })
            .then(blob => {
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = url;
                a.download = "qrcodes.pdf";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                document.getElementById("responseMessage").innerText = "PDF generated successfully!";
            })
            .catch(error => {
                document.getElementById("responseMessage").innerText = "Error: " + error.message;
            });
        }
    </script>
</body>
</html>
