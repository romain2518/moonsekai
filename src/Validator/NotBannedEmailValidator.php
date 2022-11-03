<?php

namespace App\Validator;

use App\Repository\BanRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotBannedEmailValidator extends ConstraintValidator
{
    public function __construct(
        private BanRepository $banRepository
    ) {
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotBannedEmail) {
            throw new UnexpectedTypeException($constraint, Country::class);
        }

        /** @var App\Validator\NotBannedEmail $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $targetedBan = $this->banRepository->findOneBy(['email' => $value]);
        if (null !== $targetedBan) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
