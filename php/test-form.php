<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mail = new PHPMailer(true);

header('Content-Type: application/json'); // Ensure the response is JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
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
            echo json_encode(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.']);
            exit;
        }

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('cpt.anything@gmail.com', 'Computer Anything Forms');
        $mail->addAddress('cpt.anything@gmail.com');
        $mail->addAddress('shlacker2020@outlook.com');
        $mail->Subject = 'New Test Form Submission';
        $mail->Body = "You have received a new message from the PHP test form:\n\n" .
                      "Name: $name\n" .
                      "Email: $email\n" .
                      "File Attached: " . ($attachment ? $attachment['name'] : 'No file attached') . "\n\n" .
                      "Message:\n$message";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Failed to send email. Error: {$mail->ErrorInfo}"]);
    exit;
}
?>
