<?php

namespace Nails\CustomForms\Admin\Permission\Response;

use Nails\Admin\Interfaces\Permission;

class Delete implements Permission
{
    public function label(): string
    {
        return 'Can delete responses';
    }

    public function group(): string
    {
        return 'Responses';
    }
}
