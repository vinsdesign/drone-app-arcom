<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission - Drone Log Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333333;
        }
        p {
            color: #555555;
            line-height: 1.6;
        }
        .strong {
            font-weight: bold;
            color: #333333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Contact Form Submission - Drone Log Book</h2>
        <p><span class="strong">Email:</span> {{ $data['email'] }}</p>
        <p><span class="strong">Phone:</span> {{ $data['number'] }}</p>
        <p><span class="strong">Category:</span> {{ $data['category'] }}</p>
        <p><span class="strong">Subject:</span> {{ $data['subject'] }}</p>
        <p><span class="strong">Message:</span></p>
        <p>{{ $data['message'] }}</p>
    </div>
</body>
</html>
