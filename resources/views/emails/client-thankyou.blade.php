<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333;">
    <header style="background:#f5f5f5; padding:20px; text-align:center;">
        <h2>{{ $footer->footer_heading ?? 'Our Company' }}</h2>
    </header>

    <main style="padding:20px;">
        <p>Hi {{ $data['name'] }},</p>
        <p>Thank you for contacting us. We have received your enquiry and will get back to you shortly.</p>
        <p><strong>Your Message:</strong><br>{{ $data['message'] }}</p>
    </main>

    <footer style="background:#f5f5f5; padding:20px; text-align:center;">
        <p>{{ $footer->address_line1 ?? '' }} {{ $footer->address_line2 ?? '' }}</p>
        <p>Phone: {{ $footer->phone ?? '' }} | Email: {{ $footer->email ?? '' }}</p>
    </footer>
</body>
</html>
