<?php
declare(strict_types=1);

namespace PSA\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class ChangePasswordForm extends Form
{
    public function initialize()
    {
        // Password
        $password = new Password('password');
        $password->addValidators([
            new PresenceOf([
                'message' => 'Password is required',
            ]),
            new StringLength([
                'min'            => 8,
                'messageMinimum' => 'Password is too short. Minimum 8 characters',
            ]),
            new Confirmation([
                'message' => 'Password doesn\'t match confirmation',
                'with'    => 'confirmPassword',
            ]),
        ]);

        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('confirmPassword');
        $confirmPassword->addValidators([
            new PresenceOf([
                'message' => 'The confirmation password is required',
            ]),
        ]);

        // CSRF
        $csrf = new Hidden('csrf');
        $csrf->addValidator(new Identical([
            'value'   => $this->security->getRequestToken(),
            'message' => 'CSRF validation failed',
        ]));
        $csrf->clear();

        $this->add($csrf);

        $this->add($confirmPassword);
    }
}
