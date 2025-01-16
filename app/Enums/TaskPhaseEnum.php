<?php

namespace App\Enums;

enum TaskPhaseEnum: string
{
    case BACKLOG = 'Backlog';
    case STARTED = 'Started';
    case IN_REVIEW = 'In Review';
    case DONE = 'Done';
}
