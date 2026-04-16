<?php

return [
    'login_success' => 'Login successful.',
    'logout_success' => 'Logout successful.',
    'register_success' => 'Registration successful.',
    'password_reset_success' => 'Password reset successfully.',
    'invalid_credentials' => 'Invalid credentials.',
    'throttle' => 'Limit reached, please try again in :seconds seconds!',
    'forbidden' => 'You do not have permission to perform this action.',
    'not_subscribed' => 'Subscribers only. Please check your plan to continue.',
    'forgot_password_info' => 'If the email <strong>:email</strong> is in our database, you will receive a reset link shortly.',
    'reset_password_mail' => [
        'subject' => ':app - Password Reset',
        'greeting' => 'Hello, :name',
        'line_request' => 'You are receiving this email because we received a password reset request for your account.',
        'action' => 'Reset Password',
        'line_expire' => 'This password reset link will expire in :count minutes.',
        'line_no_action' => 'If you did not request a password reset, no further action is required.',
        'salutation' => 'Regards, Team :app',
    ],
];
