<?php

namespace Nails\CustomForms\Admin\Permission\Form;

use Nails\Admin\Interfaces\Permission;

class Edit implements Permission
{
    public function label(): string
    {
        return 'Can edit forms';
    }

    public function group(): string
    {
        return 'Forms';
    }
}
