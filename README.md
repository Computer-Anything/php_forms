# simple, functional, changeable

## Steps

1. **Install dependencies**: `composer require phpmailer/phpmailer vlucas/phpdotenv`
   This command will install the necessary libraries for sending emails and managing environment variables.
2. **Create a .env file**: Copy the `.env.example` file to `.env` and fill in your SMTP credentials.
3. **Run the script**: Use the command line to run the script: `php send_email.php`
   This command will execute the PHP script that sends a test email using the SMTP credentials provided in the `.env` file.
4. **Check your email**: After running the script, check your inbox for the test email.
