<?php

/**
 * UserFormType type.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class for temporary data in form.
 */
class UserFormData extends AbstractType
{
    public ?string $currentEmail = null;
    public ?string $newEmail = null;
    public ?string $currentPassword = null;
    public ?string $newPassword = null;
}
