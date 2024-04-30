<?php

namespace App\Enums;

enum TaskPhaseEnum: string
{
    case BACKLOG = 'Backlog';
    case STARTED = 'Started';
    case IN_PROGRESS = 'In Progress';
    case DONE = 'Done';
}