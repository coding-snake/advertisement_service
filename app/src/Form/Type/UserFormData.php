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
    public ?string $current_email = null;
    public ?string $new_email = null;
    public ?string $current_password = null;
    public ?string $new_password = null;
}
