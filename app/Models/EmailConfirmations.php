<?php
declare(strict_types=1);

namespace PSA\Models;

use Phalcon\Mvc\Model;

/**
 * EmailConfirmations
 * Stores the reset password codes and their evolution
 *
 * @method static EmailConfirmations findFirstByCode(string $code)
 * @property Users $user
 */
class EmailConfirmations extends Model
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
     * @var integer
     */
    public $createdAt;

    /**
     * @var string
     */
    public $confirmed;

    /**
     * @var integer
     */
    public $updatedAtAt;

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
        $this->confirmed = 0;
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
     * Send a confirmation e-mail to the user after create the account
     */
    public function afterCreate()
    {
        $this->getDI()
            ->getMail()
            ->send([
                $this->user->email => $this->user->name,
            ], "Please confirm your email", 'confirmation', [
                'confirmUrl' => '/confirm/' . $this->code . '/' . $this->user->email,
            ]);
    }
}
