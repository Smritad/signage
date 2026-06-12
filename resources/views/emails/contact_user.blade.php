<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Thank You for Your Enquiry</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; background-color:#f9f9f9; }
    .container { max-width:600px; margin:20px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 0 15px rgba(0,0,0,0.1); }
    .header { text-align:center; padding:20px; background:#fff; }
    .header img { max-width: 200px; height:auto; }
    .body { padding:25px 20px; color:#333; }
    .body h2 { margin-top:0; font-size:22px; }
    .body p { font-size:16px; line-height:1.5; }
    .details { background:#f1f1f1; padding:15px; border-radius:5px; margin-top:15px; }
    .details p { margin:5px 0; font-size:15px; }
    .btn-home { display:inline-block; margin-top:20px; background:#3490dc; color:#fff; text-decoration:none; padding:12px 25px; border-radius:5px; font-size:16px; }
    .footer { text-align:center; padding:15px; font-size:14px; color:#666; margin-top:20px; }
    @media(max-width:480px){ 
        .body { padding:15px 10px; } 
        .btn-home { padding:10px 20px; font-size:14px; } 
    }
</style>
</head>
<body>
<div class="container">
    <!-- Header with Logo -->
    <div class="header" style="background-color: #000; padding: 15px; text-align: center;">
    <img src="https://anvayafoundation.com/signage/frontend/assets/images/logo/logo.webp" alt="Signage Logo">
</div>

    <!-- Body -->
    <div class="body">
        <h2>Hello {{ $details['name'] }},</h2>
        <p>We have received your enquiry. Our team will get back to you shortly.</p>

        <div class="details">
            <p><strong>Email:</strong> {{ $details['email'] }}</p>
            <p><strong>Phone:</strong> {{ $details['phone'] }}</p>
            <p><strong>Company:</strong> {{ $details['company'] ?: 'N/A' }}</p>
            <p><strong>Message:</strong> {{ $details['message'] }}</p>
        </div>

        <!--<a href="{{ url('/') }}" class="btn-home">Go to Home</a>-->
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; {{ date('Y') }} Signage Wellness. All Rights Reserved.
    </div>
</div>
</body>
</html>
