<?php

namespace Nails\CustomForms\Admin\Permission\Response;

use Nails\Admin\Interfaces\Permission;

class View implements Permission
{
    public function label(): string
    {
        return 'Can view responses';
    }

    public function group(): string
    {
        return 'Responses';
    }
}
