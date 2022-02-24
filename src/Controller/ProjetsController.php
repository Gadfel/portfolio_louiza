<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Repository\ProjetsRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjetsController extends AbstractController
{
    #[Route('/projet', name: 'projets_index')]
    public function index(ProjetsRepository $projetsRepository): Response
    {
        
        $projet = $projetsRepository->findAll();
        return $this->render('projets/index.html.twig', [
            'projets' => $projet,
        ]);
    }
    
    #[Route('/admin/projet', name: 'admin_projets_index')]
    public function adminIndex(ProjetsRepository $projetsRepository): Response
    {
        $projet = $projetsRepository->findAll();
        return $this->render('admin/projets.html.twig', [
            'projet' => $projet
        ]);
    }

    #[Route('/admin/projets/create', name: 'projets_create')]
    public function create(Request $request, ManagerRegistry $managerRegistry)
    {
        $projets = new Projets();
        $form = $this->createForm(ProjetsType::class, $projets); 
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
  
            $infoImg = $form['img']->getData(); //récuperer les info de l'image
            $extentionImg = $infoImg->guessExtension(); //récuperer l'extention e l'image
            $nomImg = time() . '-1.' . $extentionImg; //créer un nom unique pour l'image 
            $infoImg->move($this->getParameter('dossier_photos_img'), $nomImg); //télécharger l'image dans le dossier adéquat
            $projets->setImg($nomImg); //défnit le nom de l'image dans la bdd
            
            $manager = $managerRegistry->getManager();
            $manager->persist($projets);
            $manager->flush();

            $this->addFlash('success', 'le projet  a bien été ajouter'); // message de succes

            return $this->redirectToRoute('admin_projets_index');
        }
        
        return $this->render( 'admin/projetsForm.html.twig',[
            
                'projetsForm' => $form->createView()
            ]
        );
    }

    #[Route('/admin/projets/update/{id}', name: 'projets_update')]
    public function update(ProjetsRepository $projetsRepository, int $id, Request $request)
    {
        $projets = $projetsRepository->find($id); //récuperer l'id  
        $form = $this->createForm(ProjetsType::class, $projets);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $infoImg = $form['img']->getData();
            $nomOldImg = $projets->getImg();
            if ($infoImg !== null) {
                $cheminOldImg = $this->getParameter('dossier_photos_img') . '/' . $nomOldImg;
                if (file_exists($cheminOldImg)) {
                    unlink($cheminOldImg);
                }
                $extentionImg = $infoImg->guessExtension();
                $nomImg = time() . '-1.' . $extentionImg;
                $infoImg->move($this->getParameter('dossier_photos_img'), $nomImg);
                $projets->setImg($nomOldImg);
            } else {
                $projets->setImg($nomOldImg);
            }
        }

        return $this->render('admin/projetsForm.html.twig', [
            'projetsForm' => $form->createView(),
            'projets' => $projets
        ]);
    }

    #[Route('/admin/projets/delete/{id}', name: 'projets_delete')]
    public function delete(
        ProjetsRepository $projetsRepository,
        int $id,
        ManagerRegistry $managerRegistry
    ) {
        $projets = $projetsRepository->find($id); // récuperer la maison à suprimer en bdd
        $nomImg = $projets->getImg();
        if ($nomImg !== null) {
            $chemainImg = $this->getParameter('dossier_photos_img') . '/' . $nomImg; //reconstituer  le chemain de l'image
            if (file_exists($chemainImg)) { //verifier si le fichier existe
                unlink($chemainImg); // suprimer les images 
            }
        }

        $manager = $managerRegistry->getManager();
        $manager->remove($projets);
        $manager->flush();
        $this->addFlash('success', 'le projet à été bien suprimée'); // message de succés 
        return $this->redirectToRoute('admin_projets_index');
    }
    
    
}