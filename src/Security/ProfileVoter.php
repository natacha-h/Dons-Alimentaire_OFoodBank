<?php 
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProfileVoter extends Voter
{
    const VIEW = 'view';
    
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Je récupère le user authentifié
        $authenticatedUser = $token->getUser();

        /** @var User $user */
        $user = $subject;

        // Si l'user authentifié correspond à l'id de user, retourne true, sinon renvoie un page 500 avec écrit "No Way"
        if($authenticatedUser === $user)
        {
             return true;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        throw new \LogicException('No Way !');
    }
}
