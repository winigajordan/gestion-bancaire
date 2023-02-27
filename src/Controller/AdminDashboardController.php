<?php

namespace App\Controller;

use App\Entity\TypeCompte;
use App\Entity\TypeOperation;
use App\Repository\TypeCompteRepository;
use App\Repository\TypeOperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    private EntityManagerInterface $manager;
    private TypeCompteRepository $typeCompteRepository;
    private TypeOperationRepository $typeOperationRepository;

    /**
     * @param EntityManagerInterface $manager
     * @param TypeCompteRepository $typeCompteRepository
     * @param TypeOperationRepository $typeOperationRepository
     */
    public function __construct(EntityManagerInterface $manager, TypeCompteRepository $typeCompteRepository, TypeOperationRepository $typeOperationRepository)
    {
        $this->manager = $manager;
        $this->typeCompteRepository = $typeCompteRepository;
        $this->typeOperationRepository = $typeOperationRepository;
    }


    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }

    #[Route('/type/compte', name: 'admin_load_type_compte')]
    public function loadTypeCompte(): Response
    {
        return $this->render('admin_dashboard/type.compte.html.twig', [
            'types'=>$this->typeCompteRepository->findAll()
        ]);
    }

    #[Route('/type/compte/add', name: 'admin_load_type_compte_add')]
    public function addTypeCompte(Request $request): Response
    {
        $data = $request->request;
        //dd($data);
        $type = (new TypeCompte())
            ->setNom($data->get('nom'))
            ->setDescription($data->get('description'))
            ->setFraisOuverture($data->get('fraisOuverture'))
            ->setSoldeMin($data->get('solde'))
            ->setPlafondRetrait($data->get('plafond'))
            ->setTauxInteret($data->get('tauxInteret'))
            ->setCode($data->get('code'))
            ->setTauxInteretDecouvert($data->get('tauxDecouvert'));
        $this->manager->persist($type);
        $this->manager->flush();
        $this->addFlash('done', 'Type de compte enregistre');
        return $this->redirectToRoute('admin_load_type_compte');
    }

    #[Route('/type/compte/details/{id}', name: 'admin_load_type_compte_detsils')]
    public function showTypeCompte($id): Response
    {
        return $this->render('admin_dashboard/type.compte.html.twig', [
            'types'=>$this->typeCompteRepository->findAll(),
            'selected'=>$this->typeCompteRepository->find($id)
        ]);
    }

    #[Route('/type/compte/update', name: 'admin_load_type_compte_update')]
    public function updateTypeCompte(Request $request): Response
    {
        $data = $request->request;
        $type = $this->typeCompteRepository->find($data->get('id'));

           ( $type)->setNom($data->get('nom'))
            ->setDescription($data->get('description'))
            ->setFraisOuverture($data->get('fraisOuverture'))
            ->setSoldeMin($data->get('solde'))
            ->setPlafondRetrait($data->get('plafond'))
            ->setTauxInteret($data->get('tauxInteret'))
            ->setCode($data->get('code'))
            ->setTauxInteretDecouvert($data->get('tauxDecouvert'));

        $this->manager->persist($type);
        $this->manager->flush();
        $this->addFlash('done', 'Type de compte modifie');
        return $this->redirectToRoute('admin_load_type_compte');
    }

    #[Route('/type/operation', name: 'admin_load_type_operation')]
    public function loadTypeOperation(): Response
    {
        return $this->render('admin_dashboard/type.operation.html.twig', [
            'types'=>$this->typeOperationRepository->findAll()
        ]);
    }

    #[Route('/type/operation/add', name: 'admin_load_type_operation_add')]
    public function addTypeOperation(Request $request): Response
    {
        $data = $request->request;
        $type = (new TypeOperation())
            ->setLibelle($data->get('libelle'))
            ->setSens($data->get('sens'))
            ->setDescription($data->get('description'));
        $this->manager->persist($type);
        $this->manager->flush();
        $this->addFlash('done', 'Enregistrement effectuee');
        return $this->redirectToRoute('admin_load_type_operation');
    }

    #[Route('/type/operation/details/{id}', name: 'admin_load_type_operation_details')]
    public function showTypeOperation($id): Response
    {
        return $this->render('admin_dashboard/type.operation.html.twig', [
            'types'=>$this->typeOperationRepository->findAll(),
            'selected'=>$this->typeOperationRepository->find($id)
        ]);
    }

    #[Route('/type/operation/update', name: 'admin_load_type_operation_update')]
    public function updateTypeOperation(Request $request): Response
    {
        $data = $request->request;
        $type = ($this->typeOperationRepository->find($data->get('id')))
            ->setLibelle($data->get('libelle'))
            ->setSens($data->get('sens'))
            ->setDescription($data->get('description'));
        $this->manager->persist($type);
        $this->manager->flush();
        $this->addFlash('done', 'Modification effectuee');
        return $this->redirectToRoute('admin_load_type_operation');
    }

}


















