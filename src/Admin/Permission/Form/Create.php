<?php

namespace Nails\CustomForms\Admin\Permission\Form;

use Nails\Admin\Interfaces\Permission;

class Create implements Permission
{
    public function label(): string
    {
        return 'Can create forms';
    }

    public function group(): string
    {
        return 'Forms';
    }
}
