<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\EmployeRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;


use App\Entity\Employe;
use App\Entity\Service;


class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprise", name="entreprise")
     */
    public function index()
    {
        return $this->render('entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
        ]);
    }

    /**
     * @Route("/entreprise/liste", name="listemployes")
     */
    public function FindEmployes(EmployeRepository $repo)
    {
        $employes = $repo->findAll();
        return $this->render('entreprise/listemployes.html.twig',[
            'controller_name' => 'EntrepriseController',
            'employes' => $employes
        ]);
    }
     /**
     * @Route("/ ", name="accueil")
     */
    public function accueil()
    {
        return $this->render('entreprise/accueil.html.twig');
    }

     /**
     * @Route("/entreprise/new ", name="entreprisecreate")
     * @Route("/entreprise/{id}/ajout ", name="employeupdate")
     */
    
    public function form(Employe $employe=null,Request $requete , ObjectManager $manager)
    {
        if(!$employe)
        {
            $employe = new Employe();
        }
       
        $form = $this->createFormBuilder($employe)
                     ->add('matricule')
                     ->add('nomComplet')
                     ->add('dateNaissance', DateType::class,[
                         'widget' => 'single_text',
                        ])
                     ->add('salaire')
                     ->add('idService', EntityType::class,[
                         'class' => Service::class,
                         'choice_label' =>'nomservice',
                     ])
                     ->getForm();
        $form->handleRequest($requete);      
        if($form->isSubmitted() && $form->isValid())  
        {
            $manager->persist($employe);
            $manager->flush();
            return $this->redirectToRoute("listemployes");
        }    
        return $this->render('entreprise/create.html.twig',[
            'formEmploye' =>$form->createView(),
            'modeajout' => $employe->getId() !== null
           
        ]);
    }
}
