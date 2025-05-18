<!DOCTYPE html>
<html>
<head>
    <title>Alert Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #f8f8f8; border: 1px solid #ddd; padding: 20px; border-radius: 5px;">
        <h1 style="color: #e53e3e;">🚨 Alert Triggered</h1>
        
        <p><strong>Rule:</strong> {{ $data['rule_id'] ?? 'N/A' }}</p>
        <p><strong>Level:</strong> {{ $data['level'] ?? 'N/A' }}</p>
        <p><strong>Host:</strong> {{ $data['host'] ?? 'N/A' }}</p>
        <p><strong>Summary:</strong> {{ $data['summary'] ?? 'N/A' }}</p>
        
        @if(isset($data['payload']))
        <pre style="background: #f1f1f1; padding: 10px; border-radius: 3px; overflow: auto;">{{ json_encode($data['payload'] ?? [], JSON_PRETTY_PRINT) }}</pre>
        @endif
        
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
