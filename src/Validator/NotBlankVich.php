<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraints\NotBlank;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotBlankVich extends NotBlank
{
    public $target = null;
    public $message = null;

    #[HasNamedArguments]
    public function __construct(string $target, string $message, array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $message, null, null, $groups, $payload);

        $this->target = $target;
    }
}