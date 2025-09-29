<?php

namespace App\Security;

use App\Entity\Course;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CourseVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Course) {
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

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Course $course */
        $course = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($course, $user),
            self::EDIT => $this->canEdit($course, $user, $vote),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Course $course, User $user): bool
    {
        return $course->getStudents()->contains($user) || $course->getTeachers()->contains($user);
    }

    private function canEdit(Course $course, User $user, ?Vote $vote): bool
    {
        if ($course->getTeachers()->contains($user)) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not the author of this course (id: %d).',
            $user->getEmail(), $course->getId()
        ));

        return false;
    }
}
