# Laravel Temporary Tokens

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mydaniel/laravel-temporary-tokens.svg?style=flat-square)](https://packagist.org/packages/mydaniel/laravel-temporary-tokens)
[![Total Downloads](https://img.shields.io/packagist/dt/mydaniel/laravel-temporary-tokens.svg?style=flat-square)](https://packagist.org/packages/mydaniel/laravel-temporary-tokens)
[![License](https://img.shields.io/packagist/l/mydaniel/laravel-temporary-tokens.svg?style=flat-square)](https://github.com/daniyousefifar/laravel-temporary-tokens/blob/main/LICENSE.md)

A simple and flexible Laravel package that allows you to generate, manage, and validate temporary tokens, One-Time
Passwords (OTPs), or PINs for tasks like authentication, email verification, password resets, and more.

## Features

- Generate numeric tokens of any length.
- Set expiration dates for tokens.
- Limit the number of times a token can be used.
- Attach arbitrary data (metadata) to any token.
- Associate tokens with any Eloquent model (e.g., `User`).
- Includes an Artisan command to automatically prune expired tokens.
- Fully configurable with no external dependencies.

## Installation

You can install the package via Composer:

```bash
composer require mydaniel/laravel-temporary-tokens
```

Next, you should publish the configuration and migration files using the `vendor:publish` command:

```bash
php artisan vendor:publish --provider="MyDaniel\TemporaryTokens\TemporaryTokensServiceProvider"
```

Finally, run the migration to create the `temporary_tokens` table:

```bash
php artisan migrate
```

## Usage

### 1\. Preparing Your Model

First, add the `HasTemporaryTokens` trait to the model you wish to generate tokens for (e.g., your `User` model).

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use MyDaniel\TemporaryTokens\Traits\HasTemporaryTokens;

class User extends Authenticatable
{
    use HasTemporaryTokens;

    // ...
}
```

### 2\. Creating a Token

You can easily create new tokens using the fluent `TokenBuilder`.

**Create a simple 6-digit token for a user:**

```php
$user = User::find(1);

// The `temporaryTokenBuilder` method comes from the HasTemporaryTokens trait
$temporaryToken = $user->temporaryTokenBuilder()->create();

echo $temporaryToken->token; // e.g., "123456"
```

**Create a token with custom settings:**

```php
$user = User::find(1);

$temporaryToken = $user->temporaryTokenBuilder()
    ->setType('email-verification')       // Set a token type
    ->setTokenLength(5)                   // Generate a 5-digit token
    ->setUsageLimit(1)                    // Allow the token to be used only once
    ->setExpireDate(now()->addMinutes(15)) // Set a 15-minute expiration
    ->setMetadata(['source' => 'registration']) // Attach custom data
    ->create();
```

### 3\. Finding and Validating a Token

To find and check the validity of a token, you can use the methods on the `TemporaryToken` model.

```php
use MyDaniel\TemporaryTokens\Models\TemporaryToken;

$userToken = '123456';
$tokenType = 'email-verification';

// Find a token that is not expired and has not exceeded its usage limit
$token = TemporaryToken::findValid($userToken, $tokenType);

if ($token) {
    // The token is valid
    echo "Token is valid!";

    // You can also check if the token belongs to the correct user
    if ($token->tokenable_id === $user->id) {
        // ...
    }
}
```

The `isValid()` method is also available on a `TemporaryToken` instance to perform the same check:

```php
if ($token && $token->isValid()) {
    // ...
}
```

### 4\. Using a Token

After a token has been successfully used, you can increment its usage counter.

```php
// Mark the token as "used"
$token->use();

// If the token reaches its usage limit, `hasExceededMaxUsage()` will return true
if ($token->hasExceededMaxUsage()) {
    echo "This token cannot be used anymore.";
}
```

## Pruning Expired Tokens

You can remove expired tokens from your database by running the provided Artisan command.

```bash
php artisan temporary-tokens:prune-expired
```

By default, this command will prune tokens that expired more than 24 hours ago. To customize this, you can either use the `--hours` option or change the `prune_expired_after_hours` value in the `config/temporary-tokens.php` file.

```bash
# Prune tokens that expired more than 48 hours ago
php artisan temporary-tokens:prune-expired --hours=48
```

It is recommended to schedule this command to run daily in your `app/Console/Kernel.php` file.

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('temporary-tokens:prune-expired')->daily();
}
```

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome\! Please feel free to fork the repository and submit a pull request.

## Security Vulnerabilities

If you discover a security vulnerability within this package, please send an e-mail to Daniel Yousefi Far at `daniyousefifar@gmail.com`. All security vulnerabilities will be promptly addressed.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
