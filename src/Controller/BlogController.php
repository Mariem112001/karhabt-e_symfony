<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route("/", name="blog")
     */
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $articles = $paginator->paginate($articleRepository->findAll(),
        $request->query->getInt('page', 1),
        5);

      
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

     /**
     * @Route("article/new", name="article_new")
    */
    public function new(Request $request, FlashyNotifier $flashyNotifier, SluggerInterface $slugger)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $articleImage */
            $articleImage = $form->get('image')->getData();
    
            if ($articleImage) {
                $originalFilename = pathinfo($articleImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$articleImage->guessExtension();
    
                try {
                    $articleImage->move(
                        $this->getParameter('articles_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
    
                $article->setImage($newFilename);
            }
    
            $article->setCreatedAt(new \DateTime());
            $article->setUser($this->security->getUser());
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
    
            $flashyNotifier->success('Article successfully created!');
            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }
    
        return $this->render('blog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     /**
     * @Route("article/{id}/edit", name="article_edit")
    */
    public function edit(Request $request, Article $article, FlashyNotifier $flashyNotifier, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $articleImage */
        $articleImage = $form->get('image')->getData();

        if ($articleImage) {
            $originalFilename = pathinfo($articleImage->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$articleImage->guessExtension();

            try {
                $articleImage->move(
                    $this->getParameter('articles_directory'),
                    $newFilename
                );
                // Delete the old image file from the filesystem, if needed
            } catch (FileException $e) {
                // Handle exception if something happens during file upload
            }

            $article->setImage($newFilename);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $flashyNotifier->success('Article successfully updated!');
        return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
    }

    return $this->render('blog/edit.html.twig', [
        'editForm' => $form->createView(),
    ]);
}

    /**
     * @Route("article/{id}", name="article_show", methods={"GET", "POST"})
    */
    public function show(Article $article, Request $request , FlashyNotifier $flashyNotifier)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTime());
            $comment->setArticle($article);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $flashyNotifier->success('Comment Added Successfully!');

            return $this->redirectToRoute("article_show", ['id' => $article->getId()] );
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' =>$form->createView()
        ]);
    }

  
}
