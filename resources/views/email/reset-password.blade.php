<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 16px">

    @if(isset($formData['is_admin']) && $formData['is_admin'])
        <p>Hello Admin {{ $formData['user']->name }},</p>
        <h1>You've requested to reset your admin account password</h1>
        <p>Please click the link below to proceed:</p>
        <a href="{{ route('admin.resetPassword', $formData['token']) }}">Reset Admin Password</a>
    @else
        <p>Hi {{ $formData['user']->name }},</p>
        <h1>You have requested to reset your password</h1>
        <p>Please click the link below to reset your account password:</p>
        <a href="{{ route('front.resetPassword', $formData['token']) }}">Reset Password</a>
    @endif

    <p style="margin-top: 24px;">Thanks,<br>
</body>
</html>
