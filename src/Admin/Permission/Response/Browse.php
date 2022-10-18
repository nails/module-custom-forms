<?php

namespace Nails\CustomForms\Admin\Permission\Response;

use Nails\Admin\Interfaces\Permission;

class Browse implements Permission
{
    public function label(): string
    {
        return 'Can browse responses';
    }

    public function group(): string
    {
        return 'Responses';
    }
}
