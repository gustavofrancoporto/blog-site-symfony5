<?php


namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager,
        RouterInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    private function getCurrentUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    /**
    * @Route("/follow/{id}", name="following_follow")
    */
    public function follow(User $userToFollow)
    {
        /** @var User $currentUser */
        $currentUser = $this->getCurrentUser();
        if($userToFollow->getId() !== $currentUser->getId()) {
            $currentUser->follow($userToFollow);
            $this->entityManager->flush();
        }

        return new RedirectResponse($this->router->generate('micro_post_user',
            ['username' => $userToFollow->getUsername()]));
    }

    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     */
    public function unfollow(User $userToUnfollow)
    {
        $this->getCurrentUser()->getFollowings()->removeElement($userToUnfollow);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('micro_post_user',
            ['username' => $userToUnfollow->getUsername()]));
    }
}