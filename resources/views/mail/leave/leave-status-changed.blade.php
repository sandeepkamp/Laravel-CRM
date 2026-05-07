<!DOCTYPE html>
<html>
<head>
    <title>Leave Request Status Update</title>
</head>
<body>
    <h2>Hello {{ $leave->user->first_name }},</h2>
    <p>Your leave request from <strong>{{ \Carbon\Carbon::parse($leave->leave_from)->format('d-m-Y') }}</strong> 
       to <strong>{{ \Carbon\Carbon::parse($leave->leave_to)->format('d-m-Y') }}</strong> has been <strong>{{ $statusMessage }}</strong>.</p>
    <!--<p>Reason For Leave: {{ $leave->description }}</p>-->
    <p>Thank you!</p>
</body>
</html>
