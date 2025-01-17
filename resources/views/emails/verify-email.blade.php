<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Template</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #F5F5F5; color: #333;">
  <div style="max-width: 600px; margin: 20px auto; background: #FFFFFF; border: 1px solid #DDDDDD; border-radius: 8px; overflow: hidden;">
    <div style="text-align: center; padding: 20px;  color: white;">
      <img src="{{ url('img/elanco-logo.png') }}" alt="Elanco Logo" style="max-width: 100px;">
    </div>
    <div style="padding: 40px; text-align: center;">
      <h1 style="font-size: 24px; font-weight: 600;">Hi {{ $user->firstName }}</h1>
      <p style="font-size: 16px; color: #555555; margin: 10px 0;">
        Thank you for signing up for the Elanco EP3 Program! We're excited to have you with us.
      </p>
      <p style="font-size: 16px; color: #555555; margin: 10px 0;">
        Before you can start exploring all the resources we offer, please confirm your email address by clicking the button below on your mobile device. This link is valid for 24 hours.
      </p>
      <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 10px 20px; margin: 20px 0; background-color: #005EB8; color: white; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 4px;">
        Activate Account
      </a>
      <p style="font-size: 16px; color: #555555; margin: 10px 0;">
        If you didn't sign up, please ignore this message.
      </p>
      <p style="font-size: 16px; color: #555555; margin: 10px 0;">
        Looking forward to supporting your journey with us!
      </p>
    </div>
    <div style="text-align: center; padding: 10px; background-color: #F9F9F9; font-size: 12px; color: #888888;">
      <p>
        This message was sent to <a href="mailto:mydigital@elancoah.com" style="color: #005EB8; text-decoration: none;">mydigital@elancoah.com</a>.
        If you have questions or complaints, please
        <a href="tel:02-2128720-5" style="color: #005EB8; text-decoration: none;">contact us</a>.
      </p>
      <p>Elanco Malaysia Sdn. Bhd. <br/> Unit 5.04, Level 5 & 6, Tower Block, The Bousteador, No. 10 Jalan PJU 7/6, <br/> Mutiara Damansara, 47800 Petaling Jaya, Selangor, MALAYSIA</p>
      <p>
        <a href="https://privacy.elanco.com/" style="color: #005EB8; text-decoration: none;">Terms of Use</a> |
        <a href="https://privacy.elanco.com/" style="color: #005EB8; text-decoration: none;">Privacy Policy</a> |
        <a href="https://privacy.elanco.com/" style="color: #005EB8; text-decoration: none;">Contact Us</a>
      </p>
    </div>
  </div>
</body>
</html>
