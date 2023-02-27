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


class TransfertController extends AbstractController
{
    private ClientRepository $clientRepository;
    private CompteRepository $compteRepository;
    private TypeOperationRepository $typeOperationRepository;
    private EntityManagerInterface $manager;

    public function __construct(
        ClientRepository $clientRepository,
        CompteRepository $compteRepository,
        TypeOperationRepository $typeOperationRepository,
        EntityManagerInterface $manager,
    )
    {
        $this->clientRepository = $clientRepository;
        $this->compteRepository = $compteRepository;
        $this->typeOperationRepository = $typeOperationRepository;
        $this->envoie = $this->typeOperationRepository->find(3);
        $this->reception = $this->typeOperationRepository->find(4);
        $this->manager = $manager;
    }

    #[Route('/transfert/{client}/{compte}', name: 'app_transfert', defaults:['client'=>null, 'compte'=>null] )]
    public function index($client, $compte): Response
    {

        if ($client == null && $compte==null){
            return $this->render('transfert/index.html.twig');
        } else if ($compte == null) {
            return  $this->render('transfert/index.html.twig', [
                'client' => $this->clientRepository->findOneBy(['identifiant'=>$client]),
            ]);
        } else {
            return  $this->render('transfert/index.html.twig', [
                'client' => $this->clientRepository->findOneBy(['identifiant'=>$client]),
                'compte' => $this->compteRepository->findOneBy(['slug'=>$compte])
            ]);
        }




    }

    #[Route('/search/client', name: 'transfert_search')]
    public function search(Request $request): Response
    {

        $client = $this->clientRepository->findOneBy(['pieceIdentite'=>$request->request->get('numCarte')]);

        if ($client==null){
            $this->addFlash('error', 'Client inexistant');
            return $this->redirectToRoute('app_transfert');
        }
            return $this->redirectToRoute('app_transfert', ['client'=>$client->getIdentifiant()]);
    }

    #[Route('/search/client/account', name: 'transfert_search_account')]
    public function searchAccount(Request $request): Response
    {
        //dd('trest');
        $compte = $this->compteRepository->find($request->request->get('compte'));

        return $this->redirectToRoute('app_transfert', ['client'=>$compte->getClient()->getIdentifiant(), 'compte'=>$compte->getSlug()]);
    }

    #[Route('/send/', name: 'transfert_send')]
    public function sendMoney(Request $request): Response
    {

        $data = $request->request;
        $montant = $data->get('montant');
        $emeteur = $this->compteRepository->findOneBy(['numero'=>$data->get('emmeteur')]);


        if ($emeteur->getType()->getSoldeMin() > ( $emeteur->getSolde()-$montant)){
            $this->addFlash('erreurEnvoie', 'Montant insuffisant');
            return $this->redirectToRoute('app_transfert', ['client'=>$emeteur->getClient()->getIdentifiant(), 'compte'=>$emeteur->getSlug()]);
        }

        $receveur = $this->compteRepository->findOneBy(['numero'=>$data->get('receveur')]);
        if ($receveur == null){
            $this->addFlash('erreurEnvoie', 'Compte de reception innexistant');
            return $this->redirectToRoute('app_transfert', ['client'=>$emeteur->getClient()->getIdentifiant(), 'compte'=>$emeteur->getSlug()]);
        }


        $envoie = (new Operation())
            ->setNumero(strtoupper(uniqid()))
            ->setDate(new \DateTime())
            ->setMontant($data->get('montant'))
            ->setStatut('VALIDE')
            ->setSource($emeteur)
            ->setReceveur($receveur)
            ->setType($this->envoie);
        $emeteur->setSolde($emeteur->getSolde()-$montant);
        $receveur->setSolde($receveur->getSolde() + $montant);


        $this->manager->persist($envoie);
        $this->manager->persist($emeteur);
        $this->manager->persist($receveur);

        $this->manager->flush();
        $this->addFlash('done', 'Transfert effectue');
        return $this->redirectToRoute('app_transfert');

       }


}
