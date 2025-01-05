<?php

namespace App\Enum;

enum EventStatus: string
{
    case DRAFT = "event.status.draft";
    case CREATED = "event.status.created";
    case SENT = "event.status.sent";
    case CLOSED = "event.status.closed";
    case ARCHIVED = "event.status.archived";
}
