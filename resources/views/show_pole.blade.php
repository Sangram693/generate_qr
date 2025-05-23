<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pole Details</title>

    <!-- Bootstrap for Styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    @if(!empty($pole->batch_no))
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="message-box _success">
                <i class="fa fa-check-circle"></i>
                <h2>Utkarsh Product Verified</h2>
            </div>
        </div>
    </div>
    @endif

    <div class="container mt-5">
        <h2 class="text-center">Pole Details</h2>
        
        <div class="card p-4 shadow-lg">
            <table class="table table-bordered">
                <tr><th>Serial Number</th><td>{{ $pole->id }}</td></tr>
                <tr><th>Grade of Steel</th><td>{{ $pole->grade ?? 'N/A' }}</td></tr>
                <tr><th>Batch Number</th><td>{{ $pole->batch_no ?? 'N/A' }}</td></tr>
                <tr><th>Origin</th><td>{{ $pole->origin ?? 'N/A' }}</td></tr>
                <tr><th>Asp</th><td>{{ $pole->asp ?? 'N/A' }}</td></tr>
                <tr><th>Created At</th><td>{{ $pole->created_at }}</td></tr>
                <tr><th>Updated At</th><td>{{ $pole->updated_at }}</td></tr>
            </table>

            <div class="text-center mt-3">
                <a href="#" class="btn btn-success disabled" tabindex="-1" aria-disabled="true" style="pointer-events: none; opacity: 0.6;">
                    <i class="fa fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>

    <!-- SOS Button -->
    <div id="sos-container" style="position: fixed; right: 24px; bottom: 140px; z-index: 10000;">
        <button id="sos-button" class="btn btn-danger d-flex align-items-center justify-content-center" style="border-radius: 50%; width: 60px; height: 60px; font-size: 1.5rem;">
            SOS
        </button>
    </div>

    <!-- Chatbot Floating Action Button -->
    <div id="chatbot-container">
        <button id="chatbot-main" class="chatbot-main">
            Help
        </button>
        <div id="chatbot-box" class="chatbot-box">
            <div class="chatbot-header">
                <i class="fa fa-question-circle help-icon" title="Help"></i>
            </div>
            <button class="chatbot-action" data-url="https://www.google.com/maps/search/nearby+hospital/" title="Nearby Hospital">
                <span>Hospital</span>
            </button>
            <button class="chatbot-action" data-url="https://www.google.com/maps/search/nearby+police+station/" title="Nearby Police Station">
                <span>Police Station</span>
            </button>
            <button class="chatbot-action" data-url="https://www.google.com/maps/search/nearby+petrol+pump/" title="Nearby Petrol Pump">
                <span>Petrol Pump</span>
            </button>
        </div>
    </div>
    <style>
    /* Chatbot Container */
    #chatbot-container {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 9999;
        margin-bottom: 40px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .chatbot-main {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: #fff;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .chatbot-main:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    }
    .chatbot-box {
        position: absolute;
        bottom: 60px;
        right: 50px;
        width: 320px;
        height: 400px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 16px;
        visibility: hidden;
        opacity: 0;
        transform: scale(0.5) translateY(20px) translateX(20px);
        transform-origin: bottom right;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease-in-out, visibility 0.4s;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    #chatbot-container.open .chatbot-box {
        visibility: visible;
        opacity: 1;
        transform: scale(1) translateY(0) translateX(0);
    }
    /* Main Chatbot Button Animation */
        /* #chatbot-container.open .chatbot-main {
            transform: rotate(45deg);
        } */
    .chatbot-action {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8f9fa;
        color: #007bff;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s, color 0.3s;
        text-align: left;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .chatbot-action i {
        font-size: 1.2rem;
    }
    .chatbot-action:hover {
        background: #007bff;
        color: #fff;
    }
    .chatbot-header {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding-bottom: 8px;
        border-bottom: 1px solid #ddd;
        margin-bottom: 16px;
    }
    .help-icon {
        font-size: 1.5rem;
        color: #007bff;
        cursor: pointer;
        transition: color 0.3s;
    }
    .help-icon:hover {
        color: #0056b3;
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var chatbotMain = document.getElementById('chatbot-main');
        var chatbotContainer = document.getElementById('chatbot-container');
        var chatbotBox = document.getElementById('chatbot-box');
        var chatbotIcon = chatbotMain.querySelector('i');
        chatbotMain.addEventListener('click', function(e) {
            e.stopPropagation();
            chatbotContainer.classList.toggle('open');
            var sosContainer = document.getElementById('sos-container');
            if (chatbotContainer.classList.contains('open')) {
                sosContainer.style.display = 'none';
                chatbotIcon.classList.remove('fa-comments');
                chatbotIcon.classList.add('fa-times');
            } else {
                sosContainer.style.display = 'block';
                chatbotIcon.classList.remove('fa-times');
                chatbotIcon.classList.add('fa-comments');
            }
        });
        chatbotBox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        document.addEventListener('click', function() {
            if (chatbotContainer.classList.contains('open')) {
                chatbotContainer.classList.remove('open');
                document.getElementById('sos-container').style.display = 'block';
                chatbotIcon.classList.remove('fa-times');
                chatbotIcon.classList.add('fa-comments');
            }
        });
        var chatbotActions = document.querySelectorAll('.chatbot-action');
        chatbotActions.forEach(function(action) {
            action.addEventListener('click', function() {
                var url = this.getAttribute('data-url');
                window.open(url, '_blank');
            });
        });
        var sosButton = document.getElementById('sos-button');
        sosButton.addEventListener('click', function() {
            alert('SOS button clicked!');
        });
    });
    </script>
</body>
</html>
