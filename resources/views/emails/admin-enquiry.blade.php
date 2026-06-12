<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333;">
    <header style="background:#f5f5f5; padding:20px; text-align:center;">
        <h2>New Contact Enquiry</h2>
    </header>

    <main style="padding:20px;">
        <p><strong>Name:</strong> {{ $data['name'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Contact:</strong> {{ $data['contact'] }}</p>
        <p><strong>Company:</strong> {{ $data['company'] ?? 'N/A' }}</p>
        <p><strong>Message:</strong><br>{{ $data['message'] }}</p>
    </main>

    <footer style="background:#f5f5f5; padding:20px; text-align:center;">
        <p>{{ $footer->address_line1 ?? '' }} {{ $footer->address_line2 ?? '' }}</p>
        <p>Phone: {{ $footer->phone ?? '' }} | Email: {{ $footer->email ?? '' }}</p>
    </footer>
</body>
</html>
