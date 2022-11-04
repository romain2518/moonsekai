<?php

namespace App\Validator;

use App\Validator\NotBlankVich;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlankValidator;

class NotBlankVichValidator extends NotBlankValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof NotBlankVich && $constraint->target) {
            $targetValue = PropertyAccess::createPropertyAccessor()->getValue($this->context->getObject(), $constraint->target);

            if (!empty($targetValue)) {
                return;
            }
        }

        parent::validate($value, $constraint);
    }
}