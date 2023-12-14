<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient = $_POST["recipient"];
    $message = $_POST["message"];

    // Your original PHP code for sending SMS goes here
    $send_data = [
        'sender_id' => "PhilSMS",
        'recipient' => $recipient,
        'message' => $message,
    ];

    $token = "111|CIt6685cpJfmQbCJQjjgnHyqIsJTXdxl1izbTkJ8 ";

    $parameters = json_encode($send_data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://app.philsms.com/api/v3/sms/send");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $token",
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $get_sms_status = curl_exec($ch);
    curl_close($ch);

    // Display the response
    echo "<p>SMS Status:</p>";
    echo "<pre>" . var_export(json_decode($get_sms_status, true), true) . "</pre>";
}
?>
