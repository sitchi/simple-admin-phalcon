<?php
declare(strict_types=1);

namespace PSA\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class SignUpForm extends Form
{
    /**
     * @param null $entity
     * @param array $options
     */
    public function initialize($entity = null, array $options = [])
    {
        $name = new Text('name', [
            'placeholder' => 'Full name',
        ]);
        $name->setLabel('Name');
        $name->addValidators([
            new PresenceOf([
                'message' => 'The name is required',
            ]),
        ]);

        $this->add($name);

        // Email
        $email = new Text('email', [
            'placeholder' => 'E-Mail',
        ]);
        $email->setLabel('E-Mail');
        $email->addValidators([
            new PresenceOf([
                'message' => 'The e-mail is required',
            ]),
            new Email([
                'message' => 'The e-mail is not valid',
            ]),
        ]);

        $this->add($email);

        // Password
        $password = new Password('password', [
            'placeholder' => 'Password',
        ]);
        $password->setLabel('Password');
        $password->addValidators([
            new PresenceOf([
                'message' => 'The password is required',
            ]),
            new StringLength([
                'min' => 8,
                'messageMinimum' => 'Password is too short. Minimum 8 characters',
            ]),
            new Confirmation([
                'message' => "Password doesn't match confirmation",
                'with' => 'confirmPassword',
            ]),
        ]);

        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('confirmPassword', [
            'placeholder' => 'Confirm Password',
        ]);
        $confirmPassword->setLabel('Confirm Password');
        $confirmPassword->addValidators([
            new PresenceOf([
                'message' => 'The confirmation password is required',
            ]),
        ]);

        $this->add($confirmPassword);

        // Remember
        $terms = new Check('terms', [
            'value' => 'yes',
        ]);

        $terms->setLabel('I agree to therm');
        $terms->addValidator(new Identical([
            'value' => 'yes',
            'message' => 'Terms and conditions must be accepted',
        ]));

        $this->add($terms);

        // CSRF
        $csrf = new Hidden('csrf');
        $csrf->addValidator(new Identical([
            'value' => $this->security->getRequestToken(),
            'message' => 'CSRF validation failed',
        ]));
        $csrf->clear();

        $this->add($csrf);

        // Sign Up
        $this->add(new Submit('signUp', [
            'class' => 'btn btn-sm btn-primary btn-block',
            'value' => 'Sign Up'
        ]));
    }

}
