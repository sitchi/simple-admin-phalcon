<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

/**
 * ResetPasswords
 * Stores the reset password codes and their evolution
 *
 * @method static ResetPasswords findFirstByCode(string $code)
 */
class ResetPasswords extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $userID;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $reset;

    /**
     * @var integer
     */
    public $createdAt;

    /**
     * @var integer
     */
    public $updatedAt;


    public function initialize()
    {
        $this->belongsTo('userID', Users::class, 'id', [
            'alias' => 'user',
        ]);
    }

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        // Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Set status to non-confirmed
        $this->reset = 0;
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        // Current datetime
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    /**
     * Send an e-mail to users allowing him/her to reset his/her password
     */
    public function afterCreate()
    {
        $this->getDI()
             ->getMail()
             ->send([
                 $this->user->email => $this->user->name,
             ], "Reset your password", 'reset', [
                 'resetUrl' => '/resetPassword/' . $this->code . '/' . $this->user->email,
             ])
        ;
    }
}
