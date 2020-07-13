<?php


namespace App\Controller;


use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use App\Service\Greeting;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @Route("/blog")
 */
class BlogController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager, RouterInterface $router,
        LoggerInterface $logger, MicroPostRepository $microPostRepository)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->logger = $logger;
        $this->microPostRepository = $microPostRepository;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        $html = $this->twig->render('blog/index.html.twig', [
            'posts' => $this->microPostRepository->findBy([], ['time' => 'DESC'])
        ]);

        return new Response($html);
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     */
    public function show(MicroPost $post)
    {
        $html = $this->twig->render('blog/post.html.twig', [ 'post' => $post ] );

        return new Response($html);
    }
}