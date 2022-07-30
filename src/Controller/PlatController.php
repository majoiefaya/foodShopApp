<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Repository\CategorieRepository;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


#[Route('/plat')]
class PlatController extends AbstractController
{

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params=$params;
    }


    #[Route('/', name: 'ListePlat', methods: ['GET'])]
    public function Acceuil(PlatRepository $platRepository): Response
    {
        return $this->render('Pages/Menu.html.twig', [
            'plats' => $platRepository->findAll(),
            'statut' => 'true'
        ]);
    }

    

    #[Route('/AjouterUnPlat', name: 'AjouterPlat', methods: ['GET', 'POST'])]
    public function Ajouter(Request $request, PlatRepository $platRepository ,CategorieRepository $categorie): Response
    {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);
        $user = $this->getUser();
        if($user!=null){
            $username=$user->getUserIdentifier();
        }
        else{
            $username="YohanMajoie";
        }
        $date=new \DateTime('@'.strtotime('now'));

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriee = $_POST['categorie']/*$form->get('categorie')->getData()*/;
            $categorieplat = $_POST['categorie'];
            $rechherche = $categorie->find($categorieplat);
            $plat->setCategoriePlat($rechherche);
            $plat->setCategorie($categoriee);
            $webpath=$this->params->get("kernel.project_dir").'/public/images/PlatImages/';
            $chemin=$webpath.$_FILES['plat']["name"]["image"];
            $destination=move_uploaded_file($_FILES['plat']['tmp_name']['image'],$chemin);
            $plat->setimage($_FILES['plat']['name']['image']);
            $plat->setCreerPar($username); 
            $plat->setCreerLe($date);
            $plat->setEnable(True);
            $plat->setStatut(True);
            $platRepository->add($plat);
            return $this->redirectToRoute('Menu', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/AjouterUnPlat.html.twig', [
            'plat' => $plat,
            'form' => $form,
            'categories'=>$categorie->findAll()
        ]);
    }

    #[Route('/InformationsDuPlatN/{id}', name: 'InfosPlat', methods: ['GET'])]
    public function InfosPlat(Plat $plat, CategorieRepository $categorie): Response
    {
        return $this->render('plat/About.html.twig', [
            'plat' => $plat,
            'categorie'=>$categorie->find($plat->getCategorie())
        ]);
    }

    #[Route('/ModifierLePlatN/{id}', name: 'ModifierPlat', methods: ['GET', 'POST'])]
    public function Modifier(Request $request, Plat $plat, PlatRepository $platRepository, CategorieRepository $categorie): Response
    {
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $webpath=$this->params->get("kernel.project_dir").'/public/images/PlatImages/';
            $chemin=$webpath.$_FILES['plat']["name"]["image"];
            $destination=move_uploaded_file($_FILES['plat']['tmp_name']['image'],$chemin);
            $plat->setimage($_FILES['plat']['name']['image']);
            $platRepository->add($plat);
            return $this->redirectToRoute('Menu', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/ModifierPlat.html.twig', [
            'plat' => $plat,
            'form' => $form,
            'categories'=>$categorie->findAll()
        ]);
    }

    #[Route('/SupprimerLePlatN/{id}', name: 'SupprimerPlat', methods: ['POST'])]
    public function Supprimer(Request $request, Plat $plat, PlatRepository $platRepository,EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getId(), $request->request->get('_token'))) {
            $plat->setStatut(false);
            $em->flush($plat);
        }

        return $this->redirectToRoute('Menu', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('/SupprimerDefinitivementLePlatN/{id}', name: 'SupprimerDef', methods: ['POST', 'GET'])]
    public function SupprimerDef(Request $request, Plat $plat, PlatRepository $platRepository): Response
    {
            $platRepository->remove($plat);

        return $this->redirectToRoute('Menu', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/RestaurerLePlatN/{id}', name: 'RestaurerPlat', methods: ['POST', 'GET'])]
    public function restaurer(Request $request, Plat $plat, PlatRepository $platRepository,EntityManagerInterface $em): Response
    {
            $plat->setStatut(true);
            $em->flush($plat);

        return $this->redirectToRoute('Menu', [], Response::HTTP_SEE_OTHER);
    }
}
