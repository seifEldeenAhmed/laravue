<?php

namespace  App\Enums;



enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    /**
     * Get the label for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
        };
    }
}
