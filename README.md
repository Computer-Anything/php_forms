# simple, functional, changeable

## Steps

1. **Install dependencies**: `composer require phpmailer/phpmailer vlucas/phpdotenv`
   This command will install the necessary libraries for sending emails and managing environment variables.
2. **Create a .env file**: Copy the `.env.example` file to `.env` and fill in your SMTP credentials.
3. **Run the php server**: cd into your project dir. Use the command `php -S localhost:8000` to start a local PHP server.
4. **Visit localhost:8000/test-form.html** in your browser: This will open the test form where you can enter your email address.
5. **Check your email**: After running the script, check your inbox for the test email.
