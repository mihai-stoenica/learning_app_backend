<?php

namespace App\Security;

use App\Entity\Assignment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AssignmentVoter extends Voter
{
    const SUBMIT = 'submit';


    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::SUBMIT])) {
            return false;
        }

        if (!$subject instanceof Assignment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $vote?->addReason('The user is not logged in.');
            return false;
        }

        /** @var Assignment $assignment */
        $assignment = $subject;

        return match($attribute) {
            self::SUBMIT => $this->canSubmit($assignment, $user, $subject),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canSubmit(Assignment $assignment, User $user, Assignment $subject): bool
    {
        $course = $assignment->getCourse();
        $students = $course->getStudents();
        if ($students->contains($user)) {
            return true;
        }
        return false;
    }

}
