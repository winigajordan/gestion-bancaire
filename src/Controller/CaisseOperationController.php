<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Repository\ClientRepository;
use App\Repository\CompteRepository;
use App\Repository\TypeOperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CaisseOperationController extends AbstractController
{
    private ClientRepository $clientRepository;
    private CompteRepository $compteRepository;
    private TypeOperationRepository $typeOperationRepository;
    private  EntityManagerInterface $manager;


    public function __construct(ClientRepository $clientRepository, CompteRepository $compteRepository, TypeOperationRepository $typeOperationRepository, EntityManagerInterface $manager)
    {
        $this->clientRepository = $clientRepository;
        $this->compteRepository = $compteRepository;
        $this->typeOperationRepository = $typeOperationRepository;
        $this->manager = $manager;

    }


    #[Route('/caisse/operation', name: 'app_caisse_operation')]
    public function index(): Response
    {
        return $this->render('caisse_operation/index.html.twig', [
            'controller_name' => 'CaisseOperationController',
        ]);
    }

    #[Route('/caisse/operation/depot', name: 'caisse_load_depot')]
    public function loadPageDepot(): Response
    {
        return $this->render('caisse_operation/depot.html.twig');
    }

    #[Route('/caisse/operation/depot/account', name: 'caisse_load_account')]
    public function loadAccount(Request $request): Response
    {
        return $this->render('caisse_operation/depot.html.twig', [
            'client'=>$this->clientRepository->findOneBy(['pieceIdentite'=>$request->request->get('numCarte')])
        ]);
    }

    #[Route('/caisse/operation/depot/traitement', name: 'caisse_depot')]
    public function depot(Request $request): Response
    {
        $data= $request->request;
        $compte = $this->compteRepository->find($data->get('compte'));
        if (!$compte->isStatut()){
            $this->addFlash('error', 'Compte bloque');
            return $this->redirectToRoute('caisse_load_depot');
        }

        $operation = (new Operation())
            ->setType($this->typeOperationRepository->find(1))
            ->setSource($compte)
            ->setDate(new \DateTime())
            ->setMontant($data->get('montant'))
            ->setNumero(strtoupper(uniqid()))
            ->setStatut("VALIDE");
        $this->manager->persist($operation);
        $compte->setSolde($compte->getSolde()+$data->get('montant'));
        $this->manager->persist($compte);


        $this->manager->flush();

        $this->addFlash('done', 'Depot effectue');
        return $this->redirectToRoute('caisse_load_depot');
    }

    #[Route('/caisse/operation/retrait', name: 'caisse_load_retrait')]
    public function loadPageRetrait(): Response
    {
        return $this->render('caisse_operation/retrait.html.twig');
    }

    #[Route('/caisse/operation/retrait/account', name: 'caisse_load_account_retrait')]
    public function loadAccountRetrait(Request $request): Response
    {
        return $this->render('caisse_operation/retrait.html.twig', [
            'client'=>$this->clientRepository->findOneBy(['pieceIdentite'=>$request->request->get('numCarte')])
        ]);
    }

    #[Route('/caisse/operation/retrait/traitement', name: 'caisse_retrait')]
    public function retrait(Request $request): Response
    {
        $data= $request->request;
        $compte = $this->compteRepository->find($data->get('compte'));
        if (!$compte->isStatut()){
            $this->addFlash('error', 'Compte bloque');
            return $this->redirectToRoute('caisse_load_retrait');
        }

        if ($compte->getType()->getSoldeMin() > $compte->getSolde()-$data->get('montant')){
            $this->addFlash('error', 'Solde dupperieur au solde minimal du compte');
            return $this->redirectToRoute('caisse_load_retrait');
        }

        $operation = (new Operation())
            ->setType($this->typeOperationRepository->find(2))
            ->setSource($compte)
            ->setDate(new \DateTime())
            ->setMontant($data->get('montant'))
            ->setNumero(strtoupper(uniqid()))
            ->setStatut("VALIDE");
        $this->manager->persist($operation);
        $compte->setSolde($compte->getSolde()-$data->get('montant'));
        $this->manager->persist($compte);


        $this->manager->flush();

        $this->addFlash('done', 'Retrait effectue');
        return $this->redirectToRoute('caisse_load_retrait');
    }


}
