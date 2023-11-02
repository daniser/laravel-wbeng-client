<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use TTBooking\WBEngine\AsyncClientInterface;
use TTBooking\WBEngine\ClientInterface;

interface Client extends AsyncClientInterface, ClientInterface
{
}
