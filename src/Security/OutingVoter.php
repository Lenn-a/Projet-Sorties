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
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            $vote?->addReason('The user is not logged in.');
            return false;
        }

        $outing = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($outing, $user);
            case self::PARTICIPATE:
                return $this->canParticipate($outing, $user);
            case self::QUIT:
                return $this->canQuit($outing, $user);
            case self::CANCEL:
                return $this->canCancel($outing, $user);
            default:
                throw new \LogicException('This code should not be reached!');
        }
    }

    private function canEdit(Outing $outing, User $user): bool
    {
        if ($user === $outing->getOrganiser() && $outing->getStatus()->getLabel() === "En création"
            || $user->getRoles() == 'ROLE_ADMIN') {
            return true;
        }
        return false;
    }

    private function canParticipate(Outing $outing, User $user): bool
    {
        if (($outing->getStatus()->getLabel() != 'Clôturée' || $outing->getStatus()->getLabel() != 'Annulée')
            && !$outing->getParticipants()->contains($user)
            && $user != $outing->getOrganiser())
        {
            return true;
        }
        return false;

    }

    private function canQuit(mixed $outing, User $user): bool
    {
        if ($outing->getParticipants()->contains($user)
            && $user != $outing->getOrganiser()
            && $outing->getStatus()->getLabel() != 'Annulée')
        {
            return true;
        }
        return false;
    }

    private function canCancel(mixed $outing, User $user): bool
    {
        if ($user == $outing->getOrganiser() && $outing->getStatus()->getLabel() == 'Ouverte'
            || $user->getRoles() == 'ROLE_ADMIN' && $outing->getStatus()->getLabel() == 'Ouverte')
        {
            return true;
        }
        return false;
    }
}
