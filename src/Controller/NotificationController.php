<?php


namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    public function __construct(NotificationRepository $notificationRepository, TokenStorageInterface $tokenStorage,
        Environment $twig, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->notificationRepository = $notificationRepository;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @Route("/unread-count", name="notification_unread")
     */
    public function unreadCount()
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();

        return new JsonResponse([
            'count' => $this->notificationRepository->findUnseenByUser($currentUser)
        ]);
    }

    /**
     * @Route("/all", name="notification_all")
     */
    public function notifications()
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();

        $html = $this->twig->render('notification/notifications.html.twig', [
            'notifications' => $this->notificationRepository->findBy([
                'seen' => false,
                'user' => $currentUser
            ])
        ]);

        return new Response($html);
    }

    /**
     * @Route("/acknowledge/all", name="notification_acknowledge_all")
     */
    public function acknowledgeAll()
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();

        $this->notificationRepository->markAllAsReadByUser($currentUser);

        return new RedirectResponse($this->router->generate('notification_all'));
    }

    /**
     * @Route("/acknowledge/{id}", name="notification_acknowledge")
     */
    public function acknowledge(Notification $notification)
    {
        $notification->setSeen(true);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('notification_all'));
    }
}