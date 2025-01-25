<!DOCTYPE html>
<html>
<head>
    <title>QR Codes</title>
    <style>
        body {
            width: {{ $page_width }}mm;
            height: {{ $page_height }}mm;
            margin: {{ $margin_top }}mm {{ $margin_right }}mm {{ $margin_bottom }}mm {{ $margin_left }}mm;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .qr-container {
            width: {{ $qr_width }}mm;
            height: {{ $qr_height }}mm;
            margin: 5mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        svg {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    @foreach ($data as $item)
        <div class="qr-container">
            {{-- <p>{{ $item }}</p> <!-- This should correctly display the Employee IDs --> --}}
            {!! QrCode::format('svg')->size($qr_width)->generate($item) !!}

        </div>
    @endforeach
</body>
</html>
