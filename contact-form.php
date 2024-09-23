<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start output buffering
ob_start();

// Log all POST data
error_log("POST data: " . print_r($_POST, true));

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $comments = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Log sanitized data
    error_log("Sanitized data: name=$name, email=$email, phone=$phone, subject=$subject, comments=$comments");

    // Validate required fields
    if ($name && $phone && $email) {
        $to_email = "craeldevelopers@gmail.com";
        $email_subject = "Inquiry From Contact Page";
        $message_body = "Dear Admin,\n\n" .
            "The user whose detail is shown below has sent this message from " . $_SERVER['HTTP_HOST'] . " dated " . date('d-m-Y') . ".\n\n" .
            "Name: " . $name . "\n" .
            "Phone: " . $phone . "\n" .
            "Email Address: " . $email . "\n" .
            "Subject: " . $subject . "\n" .
            "Message: " . $comments . "\n" .
            "User IP: " . $_SERVER['REMOTE_ADDR'] . "\n\n" .
            "Thank You!";

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'craeldevelopers@gmail.com'; // Replace with your email
            $mail->Password   = 'ixbi rixl bhth wvbo'; // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress($to_email);

            //Content
            $mail->isHTML(false);
            $mail->Subject = $email_subject;
            $mail->Body    = $message_body;

            $mail->send();
            $response = array(
                'status' => 'success',
                'message' => "Thank you, " . $name . "! Your message has been sent successfully. We'll get back to you soon."
            );
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            $response = array(
                'status' => 'error',
                'message' => "Sorry, we couldn't send your message. Error: " . $mail->ErrorInfo
            );
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => "Please fill in all required fields. Missing: " . 
                         (!$name ? 'name ' : '') . 
                         (!$phone ? 'phone ' : '') . 
                         (!$email ? 'email ' : '')
        );
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => "Invalid request method."
    );
}

// Clear the output buffer and send JSON response
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>