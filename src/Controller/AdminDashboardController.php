<?php

namespace App\Controller;

use App\Entity\Caissier;
use App\Entity\TypeCompte;
use App\Entity\TypeOperation;
use App\Repository\ClientRepository;
use App\Repository\CompteRepository;
use App\Repository\OperationRepository;
use App\Repository\TypeCompteRepository;
use App\Repository\TypeOperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    private EntityManagerInterface $manager;
    private TypeCompteRepository $typeCompteRepository;
    private TypeOperationRepository $typeOperationRepository;
    private ClientRepository $clientRepository;
    private CompteRepository $compteRepository;
    private UserPasswordHasherInterface $hasher;
    private OperationRepository $operationRepository;


    public function __construct(
        EntityManagerInterface $manager,
        TypeCompteRepository $typeCompteRepository,
        TypeOperationRepository $typeOperationRepository,
        ClientRepository $clientRepository,
        CompteRepository $compteRepository,
        UserPasswordHasherInterface $hasher,
        OperationRepository $operationRepository
    )
    {
        $this->manager = $manager;
        $this->typeCompteRepository = $typeCompteRepository;
        $this->typeOperationRepository = $typeOperationRepository;
        $this->clientRepository = $clientRepository;
        $this->compteRepository = $compteRepository;
        $this->hasher = $hasher;
        $this->operationRepository = $operationRepository;
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

    #[Route('/client', name: 'admin_load_client')]
    public function loadClient(): Response
    {
        return $this->render('admin_dashboard/compte.client.html.twig');
    }

    #[Route('/client/search', name: 'admin_compte_search')]
    public function searchClient(Request $request): Response
    {
        $client = $this->clientRepository->findOneBy(['pieceIdentite'=>$request->request->get('code')]);
        if ($client==null){
            $this->addFlash('error', 'Client introuovable | verrifiez le numero saisie');
            return $this->redirectToRoute('admin_load_client');
        }
        return $this->render('admin_dashboard/compte.client.html.twig', [
            'client'=> $client
        ]);
    }

    #[Route('/client/compte/{slug}', name: 'admin_compte_client')]
    public function loadCompteClient($slug): Response
    {

        $compte = $this->compteRepository->findOneBy(['slug'=>$slug]);
        return $this->render('admin_dashboard/compte.client.html.twig', [
            'client' => $compte->getClient(),
            'account' => $compte,
        ]);
    }

    #[Route('/caissier', name: 'admin_compte_caissier')]
    public function loadCompteCaissier(): Response
    {
        return $this->render('admin_dashboard/compte.caissier.html.twig');
    }

    #[Route('/caissier/add', name: 'admin_compte_caissier_add')]
    public function addCompteCaissier(Request $request): Response
    {
        $data = $request->request;
        $caissier = (new Caissier())
            ->setNom($data->get('nom'))
            ->setPrenom($data->get('prenom'))
            ->setIdentifiant(uniqid('ca-'))
            ->setEmail($data->get('email'));
            $caissier->setPassword($this->hasher->hashPassword($caissier, $data->get('password')));

        $this->manager->persist($caissier);
        $this->manager->flush();
        $this->addFlash('done', 'Enregistrement effectuee');
        return $this->redirectToRoute('admin_compte_caissier');
    }


    #[Route('/stat', name: 'admin_stat')]
    public function loadStat(): Response
    {
        return $this->render('admin_dashboard/stat.html.twig');
    }

    #[Route('/stat/show', name: 'admin_stat_show')]
    public function chaeckStat(Request $request): Response
    {
        $data = $request->request;
        $debut = \DateTime::createFromFormat('Y-m-d', $data->get('debut'), new \DateTimeZone('UTC'));
        $fin = \DateTime::createFromFormat('Y-m-d', $data->get('fin'), new \DateTimeZone('UTC'));
        $operations = $this->operationRepository->findByDate($debut, $fin, $this->manager);

        $retraits = 0;
        $depots = 0;
        $transfert = 0;
        foreach ($operations as $operation) {
            if ($operation->getType()->getLibelle() == 'Retrait'){
                $retraits = $retraits + $operation->getMontant();
            }
            if ($operation->getType()->getLibelle() == 'Depot'){
                $depots = $depots + $operation->getMontant();
            }
            if ($operation->getType()->getLibelle() == 'Envoie'){
                $transfert = $transfert + $operation->getMontant();
            }
        }

        $compte = $this->compteRepository->findAll();
        //nombre ce compte cree entre deux date
        $nbreCompte = 0;
        foreach ($compte as $cpt) {
            $date = $cpt->getOuverture();
            if ($date >= $debut && $date <= $fin){
                $nbreCompte++;
            }
        }

        return $this->render('admin_dashboard/stat.html.twig', [
            'retrait' => $retraits,
            'depot' => $depots,
            'transfert' => $transfert,
            'compte' => $nbreCompte,
            'operations' => $operations,
        ]);
    }


}


















