<?php

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "$label is required." : null;
}

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validateUsernameFormat(string $value): ?string
{
    if (strlen($value) < 3 || strlen($value) > 50) {
        return "Username must be between 3 and 50 characters.";
    }
    return preg_match('/^[A-Za-z0-9_.]+$/', $value)
        ? null
        : "Username can only contain letters, numbers, underscores, and periods.";
}

function validatePasswordStrength(string $value): ?string
{
    return strlen($value) >= 8 ? null : "Password must be at least 8 characters long.";
}

function validatePasswordsMatch(string $password, string $confirm): ?string
{
    return $password === $confirm ? null : "Passwords do not match.";
}

/**
 * Validates registration input coming from $_POST.
 */
function validateRegisterInput(array $post): array
{
    $username = trim($post['username'] ?? '');
    $email    = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';
    $confirm  = $post['confirm_password'] ?? '';

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateUsernameFormat($username),
        validateRequired($email, 'Email'),
        validateEmailFormat($email),
        validateRequired($password, 'Password'),
        validatePasswordStrength($password),
        validatePasswordsMatch($password, $confirm),
    ]);
    $errors = array_values($errors);

    if (empty($errors)) {
        $username = htmlspecialchars($username);
    }

    return [
        'errors' => $errors,
        'data'   => ['username' => $username, 'email' => $email, 'password' => $password],
    ];
}

/**
 * Validates login input coming from $_POST.
 */
function validateLoginInput(array $post): array
{
    $email    = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';

    $errors = array_filter([
        validateRequired($email, 'Email'),
        validateEmailFormat($email),
        validateRequired($password, 'Password'),
    ]);
    $errors = array_values($errors);

    return [
        'errors' => $errors,
        'data'   => ['email' => $email, 'password' => $password],
    ];
}
