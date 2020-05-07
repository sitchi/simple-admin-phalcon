<?php
declare(strict_types=1);

namespace PSA\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use PSA\Models\Roles;

class UsersForm extends Form
{
    /**
     * @param null $entity
     * @param array $options
     */
    public function initialize($entity = null, array $options = [])
    {
        $id = new Hidden('id');
        $this->add($id);

        $name = new Text('name', [
            'placeholder' => 'Name',
        ]);

        $name->addValidators([
            new PresenceOf([
                'message' => 'The name is required',
            ]),
        ]);

        $this->add($name);

        $email = new Text('email', [
            'placeholder' => 'Email',
        ]);

        $email->addValidators([
            new PresenceOf([
                'message' => 'The e-mail is required',
            ]),
            new Email([
                'message' => 'The e-mail is not valid',
            ]),
        ]);

        $this->add($email);

        $roles = Roles::find([
            'active = :active:',
            'bind' => [
                'active' => 1,
            ],
        ]);

        $this->add(new Select('roleID', $roles, [
            'using' => [
                'id',
                'name',
            ],
            'useEmpty' => true,
            'emptyText' => '...',
            'emptyValue' => '',
        ]));

        $this->add(new Select('banned', [
            '1' => 'Yes',
            '0' => 'No',
        ]));

        $this->add(new Select('suspended', [
            '1' => 'Yes',
            '0' => 'No',
        ]));

        $this->add(new Select('active', [
            '1' => 'Yes',
            '0' => 'No',
        ]));

        if (empty($options['edit'])) {
            // Password
            $password = new Password('password', [
                'class' => 'form-control',
                'placeholder' => 'New Password'
            ]);
            $password->addValidators([
                new PresenceOf([
                    'message' => 'Password is required',
                ]),
                new StringLength([
                    'min' => 8,
                    'messageMinimum' => 'Password is too short. Minimum 8 characters',
                ]),
            ]);
            $this->add($password);
        } else {
            // Password
            $password = new Password('newPassword', [
                'class' => 'form-control',
                'placeholder' => 'New Password'
            ]);
            if ($options['newPassword'])
                $password->addValidators([
                    new StringLength([
                        'min' => 8,
                        'messageMinimum' => 'Password is too short. Minimum 8 characters',
                    ])
                ]);
            $this->add($password);
        }
        $roles = \PSA\Models\Roles::find([
            'active = :active:',
            'bind' => [
                'active' => 1
            ]
        ]);

        $userRoles = empty($entity->id) ? null : $entity->userRolesID($entity->id);

        $rolesID = new Select('rolesID[]', $roles, [
            'using' => [
                'id',
                'name'
            ],
            'value' => $userRoles,
            'multiple' => 'multiple',
            'data-placeholder' => 'Select Roles',
            'required' => ''
        ]);

        $this->add($rolesID);

        // CSRF
        $csrf = new Hidden('csrf');
        $csrf->addValidator(new Identical([
            'value' => $this->security->getRequestToken(),
            'message' => 'CSRF validation failed',
        ]));
        $csrf->clear();
        $this->add($csrf);
    }

    public function getCsrf()
    {
        return $this->security->getToken();
    }
}
