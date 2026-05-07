<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Platform</title>
</head>
<body>
    <h2>Hello {{ $user->first_name }} {{ $user->last_name }},</h2>
    <p>Welcome to our platform! Your account has been created successfully.</p>
    <p>You can log in using the following credentials:</p>
    <ul>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Password:</strong> (The password you set during registration)</li>
    </ul>
    <p>Thank you for joining us!</p>
    <p>If you have any questions, our support team is here to help at <a href="mailto:support@email.com"></a>support@email.com</a></p>
</body>
</html>
