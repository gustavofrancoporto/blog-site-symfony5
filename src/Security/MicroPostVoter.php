<?php


namespace App\Security;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports(string $attribute, $subject)
    {
        return $subject instanceof MicroPost and in_array($attribute, [self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if($this->decisionManager->decide($token, [ User::ROLE_ADMIN ])) {
            return true;
        }
        if (!$token->getUser() instanceof User) {
            return false;
        }

        /** @var User $authenticatedUser */
        $authenticatedUser = $token->getUser();
        /** @var MicroPost $microPost */
        $microPost = $subject;

        return $microPost->getUser()->getId() === $authenticatedUser->getId();
    }
}