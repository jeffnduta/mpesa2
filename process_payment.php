<?php
// Database connection
$host = 'localhost';
$dbname = 'stretcha_kaz';
$username = 'stretcha_ddd'; // Change to your database username
$password = 'D40629378e#'; // Change to your database password

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Daraja API access token function
function generateAccessToken() {
    $consumerKey = '4DA3iPuTWAm5V2UiIcqlJCAjYKKlo8q0KFsRHEdLEqgXKK3v'; // Replace with your actual Consumer Key
    $consumerSecret = 'mt92x54vl9RG7cqeaHry5oofPVLoNZDQ2zQ3NSGUNizjxuPk7snjjnkm4AcPOixW'; // Replace with your actual Consumer Secret
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);

    if (curl_errno($curl)) {
        die(json_encode(['status' => 'error', 'message' => 'Failed to get access token.']));
    }

    $result = json_decode($curl_response);
    return $result->access_token;
}

// Normalize phone number function
function normalizePhoneNumber($phone) {
    // Remove any non-numeric characters
    $phone = preg_replace('/\D/', '', $phone);

    // Convert local formats starting with '07' or '01'
    if (substr($phone, 0, 2) === '07') {
        $phone = '254' . substr($phone, 1);
    } elseif (substr($phone, 0, 2) === '01') {
        $phone = '254' . substr($phone, 1);
    } elseif (substr($phone, 0, 4) === '+254') {
        $phone = substr($phone, 1);
    } elseif (substr($phone, 0, 4) === '2541' || substr($phone, 0, 4) === '2547') {
        // No change needed for already valid formats
    } else {
        return false; // Invalid format
    }

    // Validate if the number is exactly 12 digits long and starts with '2547'
    if (strlen($phone) === 12 && preg_match('/^2547\d{8}$/', $phone)) {
        return $phone;
    }

    // Validate if the number is in the correct format but starts with 254
    if (strlen($phone) === 12 && preg_match('/^254\d{9}$/', $phone)) {
        return $phone;
    }

    return false; // Invalid phone number
}

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = normalizePhoneNumber($_POST['phone']);
    $amount = $_POST['amount'];

    if ($phone === false) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid phone number format. Please try again.']);
        exit;
    }

    // Save form data into the database
    $stmt = $conn->prepare("INSERT INTO payments (name, phone, amount) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssd", $name, $phone, $amount);

    if ($stmt->execute()) {
        // Get the last inserted ID for reference
        $paymentID = $stmt->insert_id;
        $stmt->close();

        // Now initiate M-Pesa payment
        $paybillNumber = "174379";
        $accountReference = $name; // Using the sender's name as the account reference
        $shortcode = $paybillNumber;

        $accessToken = generateAccessToken(); // Call the function to get the access token
        if (!$accessToken) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to generate access token']);
            exit;
        }

        $timestamp = date("YmdHis");
        $lipaNaMpesaOnlinePasskey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; // Replace with your passkey
        $lipaNaMpesaPassword = base64_encode($shortcode . $lipaNaMpesaOnlinePasskey . $timestamp);
        $callbackUrl = 'https://wordsmith.stretchahand.org/mpesa_callback.php'; // Replace with your callback URL

        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; // Use sandbox for testing, change to live for production

        $curl_post_data = [
            'BusinessShortCode' => $shortcode,
            'Password' => $lipaNaMpesaPassword,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => $accountReference,
            'TransactionDesc' => "Payment for services",
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
        $curl_response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo json_encode(['status' => 'error', 'message' => 'Payment request failed: ' . curl_error($curl)]);
            exit;
        }

        $response = json_decode($curl_response, true);

        if ($response['ResponseCode'] == "0") {
            // Payment request successful
            echo json_encode(['status' => 'success', 'message' => 'Payment request sent. Please check your phone to complete the payment.']);
        } else {
            // Payment request failed
            echo json_encode(['status' => 'error', 'message' => 'Payment request failed: ' . $response['errorMessage']]);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error saving payment data: ' . $stmt->error]);
    }
}
?>
