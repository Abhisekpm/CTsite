<!DOCTYPE html>
<html>
<head>
    <title>Chocolate Therapy - Contact Form</title>
</head>
<body>
    <h2>New Contact Form Submission from {{ $data->name }}</h2>
<br>

<strong>Customer Details:</strong><br>
<strong>Name:</strong> {{ $data->name }} <br>
<strong>Email:</strong> {{ $data->email }} <br>
<strong>Subject:</strong> {{ $data->subject }} <br>
<strong>Message:</strong> {{ $data->message }} <br><br>

<p>This inquiry was submitted through the Chocolate Therapy contact form.</p>

Thank you
</body>
</html>
