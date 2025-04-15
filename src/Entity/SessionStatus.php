<?php

namespace App\Entity;

enum SessionStatus: string
{
    case Created = 'Created';
    case ModeratorApproved = 'Moderator approved';
    case JuryApproved = 'Jury approved';
    case Scheduled = 'Scheduled';
    case Rejected = 'Rejected';
}