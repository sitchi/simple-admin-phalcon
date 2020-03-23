<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;
use Phalcon\Security;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use PSA\Models\UsersRoles;

/**
 * All the users registered in the application
 */
class Users extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $mustChangePassword;

    /**
     * @var string
     */
    public $banned;

    /**
     * @var string
     */
    public $suspended;

    /**
     * @var string
     */
    public $active;

    public function initialize()
    {
        // Audit
        $this->keepSnapshots(true);
        $this->addBehavior(new \PSA\Models\Blameable());

        $this->hasMany('id', UsersAuths::class, 'userID', [
            'alias' => 'usersAuths',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system',
            ],
        ]);

        $this->hasMany('id', PasswordChanges::class, 'userID', [
            'alias' => 'passwordChanges',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system',
            ],
        ]);

        $this->hasMany('id', ResetPasswords::class, 'userID', [
            'alias' => 'resetPasswords',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system',
            ],
        ]);
    }

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        if (empty($this->password)) {
            // Generate a plain temporary password
            $tempPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

            // The user must change its password in first login
            $this->mustChangePassword = 1;

            /** @var Security $security */
            $security = $this->getDI()->getShared('security');
            // Use this password as default
            $this->password = $security->hash($tempPassword);
        } else {
            // The user must not change its password in first login
            $this->mustChangePassword = 0;
        }

        // The account must be confirmed via e-mail
        // Only require this if emails are turned on in the config, otherwise account is automatically active
        if ($this->getDI()->get('config')->useMail) {
            $this->active = 0;
        } else {
            $this->active = 1;
        }

        // The account is not suspended by default
        $this->suspended = 0;

        // The account is not banned by default
        $this->banned = 0;
    }

    /**
     * Send a confirmation e-mail to the user if the account is not active
     */
    public function afterCreate()
    {
        // Only send the confirmation email if emails are turned on in the config
        if ($this->getDI()->get('config')->useMail && $this->active == 0) {
            $emailConfirmation = new EmailConfirmations();
            $emailConfirmation->userID = $this->id;

            if ($emailConfirmation->save()) {
                $this->getDI()
                    ->getFlash()
                    ->notice('A confirmation mail has been sent to ' . $this->email);
            }
        }
    }

    /**
     * Validate that emails are unique across users
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add('email', new Uniqueness([
            "message" => "The email is already registered",
        ]));

        return $this->validate($validator);
    }


    /**
     * get all roles
     */
    public function userRoles($userID)
    {
        return UsersRoles::find("userID = '$userID'");
    }

    /**
     * get all roleID
     */
    public function userRolesID($userID)
    {
        $result = [];
        $usersRoles = UsersRoles::find("userID = '$userID'");
        foreach ($usersRoles as $value) {
            $result[] = $value->roleID;
        }
        return $result;
    }

}
