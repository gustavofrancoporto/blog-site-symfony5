<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class LikesController
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

    /**
     * @Route("/like/{id}", name="likes_like")
     */
    public function like(MicroPost $microPost)
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();

        if(!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->like($currentUser);
        $this->entityManager->flush();

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }

    /**
     * @Route("/unlike/{id}", name="likes_unlike")
     */
    public function unlike(MicroPost $microPost)
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();

        if(!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->unlike($currentUser);
        $this->entityManager->flush();

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }
}