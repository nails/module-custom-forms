<?php

namespace Nails\CustomForms\Admin\Permission\Form;

use Nails\Admin\Interfaces\Permission;

class Browse implements Permission
{
    public function label(): string
    {
        return 'Can browse forms';
    }

    public function group(): string
    {
        return 'Forms';
    }
}
