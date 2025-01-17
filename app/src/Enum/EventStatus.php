<?php

namespace App\Enum;

enum EventStatus: string
{
    case DRAFT = "event.status.draft";
    case STARTED = "event.status.started";
    case CLOSED = "event.status.closed";
    case CANCELLED = "event.status.cancelled";
}
