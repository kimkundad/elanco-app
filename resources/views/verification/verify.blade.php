<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Template</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
    .email-container {
      max-width: 600px;
      padding: 30px
      margin: 50px auto;
      background: #ffffff;
      border: 1px solid #dddddd;
      border-radius: 8px;
      overflow: hidden;
    }
    .header {
      text-align: center;
      padding: 20px;
      background-color: #005eb8;
      color: white;
    }
    .header img {
      max-width: 100px;
    }
    .content {
      padding: 40px;
      text-align: center;
    }
    .content h1 {
      font-size: 24px;
      color: #333333;
      font-weight: 600;
    }
    .content p {
      font-size: 16px;
      color: #555555;
      font-weight: 400;
      margin: 10px 0;
    }
    .button {
      display: inline-block;
      padding: 10px 20px;
      margin: 20px 0;
      background-color: #005eb8;
      color: white;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      border-radius: 4px;
    }
    .footer {
      text-align: center;
      padding: 10px;
      background-color: #f9f9f9;
      font-size: 12px;
      color: #888888;
    }
    .footer a {
      color: #005eb8;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <img src="logo-url-here" alt="Elanco Logo">
    </div>
    <div class="content">
      <h1>Hi {{ $user->firstName }}</h1>
      <p>Thank you for signing up for the Elanco EP3 Program! We're excited to have you with us.</p>
      <p>Before you can start exploring all the resources we offer, please confirm your email address by clicking the button below on your mobile device. This link is valid for 24 hours.</p>
      <a href="{{ $verificationUrl }}" class="button">Activate Account</a>
      <p>If you didn't sign up, please ignore this message.</p>
      <p>Looking forward to supporting your journey with us!</p>
    </div>
    <div class="footer">
      <p>This message was sent to <a href="mailto:venacons@gmail.com">venacons@gmail.com</a>. If you have questions or complaints, please <a href="contact-link-here">contact us</a>.</p>
      <p>Elanco, Regeringsgatan 19, 111 53, Stockholm, Sweden</p>
      <p><a href="terms-link-here">Terms of Use</a> | <a href="privacy-policy-link-here">Privacy Policy</a> | <a href="contact-us-link-here">Contact Us</a></p>
    </div>
  </div>
</body>
</html>
