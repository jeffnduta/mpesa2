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

// Get the POST data sent from Safaricom
$response = file_get_contents('php://input');
$mpesaResponse = json_decode($response, true);

// Log the full M-Pesa response for debugging
error_log("M-Pesa Response: " . print_r($mpesaResponse, true));

// Initialize variables
$transactionID = '';
$transactionStatus = 'Failed'; // Default to failed
$paymentAmount = '';
$paymentPhoneNumber = '';
$errorMsg = '';

// Check if the M-Pesa response has the necessary data
if (isset($mpesaResponse['Body']['stkCallback']['ResultCode'])) {
    $resultCode = $mpesaResponse['Body']['stkCallback']['ResultCode'];
    $resultDesc = $mpesaResponse['Body']['stkCallback']['ResultDesc'];

    // Handle different response codes
    switch ($resultCode) {
        case 0: // Payment is successful
            foreach ($mpesaResponse['Body']['stkCallback']['CallbackMetadata']['Item'] as $item) {
                switch ($item['Name']) {
                    case 'MpesaReceiptNumber':
                        $transactionID = $item['Value'];
                        break;
                    case 'Amount':
                        $paymentAmount = $item['Value'];
                        break;
                    case 'PhoneNumber':
                        $paymentPhoneNumber = $item['Value'];
                        break;
                }
            }

            // Update transaction status to "Success" in the database
            $stmt = $conn->prepare("UPDATE payments SET transaction_status = 'Success', transaction_id = ? WHERE phone = ? AND amount = ?");
            if ($stmt) {
                $stmt->bind_param("ssd", $transactionID, $paymentPhoneNumber, $paymentAmount);
                if ($stmt->execute()) {
                    $transactionStatus = 'Success'; // Mark as success
                } else {
                    $errorMsg = 'Database update failed: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $errorMsg = 'Database preparation failed: ' . $conn->error;
            }
            break;

        case 1: // Insufficient funds
            $errorMsg = 'Payment failed: Insufficient funds';
            break;

        case 1032: // Canceled by user
            $errorMsg = 'Payment failed: Canceled by user';
            break;

        default: // Other failure responses
            $errorMsg = 'Payment failed with message: ' . $resultDesc;
            break;
    }
} else {
    // Missing expected fields in the response
    $errorMsg = 'Invalid M-Pesa response structure';
}

// If the payment failed or there was an error, update the status to "Failed" in the database
if ($transactionStatus == 'Failed' && !empty($paymentPhoneNumber)) {
    $stmt = $conn->prepare("UPDATE payments SET transaction_status = 'Failed' WHERE phone = ?");
    if ($stmt) {
        $stmt->bind_param("s", $paymentPhoneNumber);
        if (!$stmt->execute()) {
            $errorMsg = 'Failed to update failed payment status: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = 'Failed to prepare the statement for failed payment: ' . $conn->error;
    }
}

// Log any errors if they occurred
if (!empty($errorMsg)) {
    error_log($errorMsg); // Log the error to your server's error log for debugging
}

// Redirect the user if the payment was successful
if ($transactionStatus == 'Success') {
    // Redirect URL (update this to your actual success page)
    $redirectUrl = 'https://wordsmith.stretchahand.org/'; 
    header("Location: $redirectUrl");
    exit();
}

// Send HTTP 200 response to Safaricom to acknowledge the callback was received
http_response_code(200);
?>
