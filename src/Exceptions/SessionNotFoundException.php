<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Exceptions;

use Illuminate\Support\ItemNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SessionNotFoundException extends ItemNotFoundException
{
    public function getInnerException(): NotFoundHttpException
    {
        return new NotFoundHttpException($this->getMessage(), $this);
    }
}
