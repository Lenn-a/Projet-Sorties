<?php

namespace App\Security;

use App\Entity\Outing;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use \Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OutingVoter extends Voter
{
    const EDIT = 'OUTING_EDIT';
    const CANCEL = 'OUTING_CANCEL';
    const PARTICIPATE = 'OUTING_PARTICIPATE';
    const QUIT = 'OUTING_QUIT';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    )
    {
    }


    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::CANCEL, self::PARTICIPATE, self::QUIT])) {
            return false;
        }

        if (!$subject instanceof Outing) {
            return false;
        }

        return true;

    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            $vote?->addReason('The user is not logged in.');
            return false;
        }

        $outing = $subject;

        return match ($attribute) {
            self::EDIT => $this->canEdit($outing, $user, $vote),
            self::PARTICIPATE => $this->canParticipate($outing, $user, $vote),
            self::QUIT => $this->canQuit($outing, $user, $vote),
            self::CANCEL => $this->canCancel($outing, $user, $vote),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEdit(Outing $outing, User $user, ?Vote $vote): bool
    {
        if ($user === $outing->getOrganiser() && $outing->getStatus()->getLabel() === "En création") {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (username: %s) is not the author of this outing (name: %s).',
            $user->getUsername(), $outing->getName()
        ));

        return false;
    }

    private function canParticipate(Outing $outing, User $user, ?Vote $vote): bool
    {
        if ($outing->getStatus()->getLabel() != 'Clôturée'
            && !$outing->getParticipants()->contains($user))
        {
            return true;
        }
        $vote?->addReason('You cannot participate in this outing.');
        return false;

    }

    private function canQuit(mixed $outing, User $user, ?Vote $vote): bool
    {
        if ($outing->getParticipants()->contains($user))
        {
            return true;
        }
        $vote?->addReason('You cannot quit an outing if you\'re not already a participant.');
        return false;
    }

    private function canCancel(mixed $outing, User $user, ?Vote $vote): bool
    {
        if ($user == $outing->getOrganiser())
        {
            return true;
        }
        $vote?->addReason('You cannot cancel an outing you have not created.');
        return false;
    }
}
