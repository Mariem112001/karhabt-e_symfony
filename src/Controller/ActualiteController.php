<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/actualites')]
class ActualiteController extends AbstractController
{
    #[Route('/', name: 'app_actualite_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ActualiteRepository $actualiteRepository): Response
    {
        $query = $actualiteRepository->createQueryBuilder('a')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5 // Items per page
        );

        return $this->render('actualite/index.html.twig', [
            'actualites' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_actualite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ActualiteRepository $actualiteRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $actualite = new Actualite();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $actualite->setImage($newFilename);
            }

            $entityManager->persist($actualite);
            $entityManager->flush();

            return $this->redirectToRoute('app_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actualite/new.html.twig', [
            'actualite' => $actualite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_actualite_show', methods: ['GET'])]
    public function show(Actualite $actualite): Response
    {
        return $this->render('actualite/show.html.twig', [
            'actualite' => $actualite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actualite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Actualite $actualite, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $originalImage = $actualite->getImage();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                if ($originalImage) {
                    // Delete the old image file
                    $oldFilePath = $this->getParameter('images_directory').'/'.$originalImage;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $actualite->setImage($newFilename);
            } elseif (!$file && $request->get('remove_image') === 'true') {
                // If checkbox to remove image is checked and no new file is uploaded
                if ($originalImage) {
                    $oldFilePath = $this->getParameter('images_directory').'/'.$originalImage;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                    $actualite->setImage(null);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actualite/edit.html.twig', [
            'actualite' => $actualite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_actualite_delete', methods: ['POST'])]
    public function delete(Request $request, Actualite $actualite, ActualiteRepository $actualiteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actualite->getId(), $request->request->get('_token'))) {
            $actualiteRepository->remove($actualite, true);
        }

        return $this->redirectToRoute('app_actualite_index', [], Response::HTTP_SEE_OTHER);
    }
}
