<?php

namespace App\Enums;

enum TicketTypeEnum: string
{
    case BUG = 'bug';
    case IMPROVMENT = 'improvment';
    case STORY = 'story';
    case SUB_TASK = 'sub task';

}
