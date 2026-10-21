<?php

use App\Models\User;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

it('keeps the authentication, authorization and password reset contracts', function () {
    expect(class_implements(User::class))
        ->toHaveKeys([Authenticatable::class, Authorizable::class, CanResetPassword::class]);
});

it('carries no email verification contract, trait or helper', function () {
    expect(class_implements(User::class))->not->toHaveKey(MustVerifyEmail::class)
        ->and(class_uses_recursive(User::class))->not->toHaveKey(MustVerifyEmailTrait::class)
        ->and(method_exists(User::class, 'hasVerifiedEmail'))->toBeFalse()
        ->and(method_exists(User::class, 'markEmailAsVerified'))->toBeFalse()
        ->and(method_exists(User::class, 'sendEmailVerificationNotification'))->toBeFalse();
});
