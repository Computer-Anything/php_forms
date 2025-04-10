<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mail = new PHPMailer(true);

// Enable verbose debug output
$mail->SMTPDebug = 2; // Set to 2 for detailed debugging output
$mail->Debugoutput = 'html'; // Output debugging information in HTML format

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $subject = htmlspecialchars($_POST['subject']);
        $attachment = $_FILES['attachment'] ?? null;
        if ($attachment && $attachment['error'] === UPLOAD_ERR_OK) {
            $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
        }
        $message = htmlspecialchars($_POST['message']);
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        // Verify reCAPTCHA
        $recaptchaSecret = $_ENV['RECAPTCHA_SECRET_KEY'];
        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaValidation = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
        $recaptchaResult = json_decode($recaptchaValidation, true);

        if (!$recaptchaResult['success']) {
            throw new Exception('reCAPTCHA verification failed. Please try again.');
        }

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cpt.anything@gmail.com';
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('cpt.anything@gmail.com', 'Duncan Murchison');
        $mail->addAddress('duncancmurchison@gmail.com');
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body = "You have received a new message from your contact form:\n\n" .
                    "Name: $name\n" .
                    "Email: $email\n" .
                    "Phone: $phone\n" .
                    "Subject: $subject\n" .
                    "File Attached: " . ($attachment ? $attachment['name'] : 'No file attached') . "\n\n" .
                    "Message:\n$message";

        $mail->send();
        echo 'Email sent successfully.';
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}
?>
