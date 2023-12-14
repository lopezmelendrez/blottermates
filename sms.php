<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhilSMS Sender</title>
</head>
<body>
    <h1>PhilSMS Sender</h1>
    
    <form action="sendsms.php" method="post">
        <label for="recipient">Recipient Number (with country code):</label>
        <input type="text" id="recipient" name="recipient" required>
        
        <br>
        
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" required></textarea>
        
        <br>
        
        <button type="submit">Send SMS</button>
    </form>
</body>
</html>
