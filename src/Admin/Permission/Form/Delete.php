<?php

namespace Nails\CustomForms\Admin\Permission\Form;

use Nails\Admin\Interfaces\Permission;

class Delete implements Permission
{
    public function label(): string
    {
        return 'Can delete forms';
    }

    public function group(): string
    {
        return 'Forms';
    }
}
