<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Repository\ClientRepository;
use App\Repository\CompteRepository;
use App\Repository\TypeCompteRepository;
use App\Repository\TypeOperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Webmozart\Assert\Tests\StaticAnalysis\null;
#[isGranted('ROLE_CLIENT')]
class ClientController extends AbstractController
{
    private CompteRepository $compteRepository;
    private TypeCompteRepository $typeCompteRepository;
    private  TypeOperationRepository $typeOperationRepository;
    private EntityManagerInterface $manager;

    /**
     * @param CompteRepository $compteRepository
     * @param TypeCompteRepository $typeCompteRepository
     * @param TypeOperationRepository $typeOperationRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(CompteRepository $compteRepository, TypeCompteRepository $typeCompteRepository, TypeOperationRepository $typeOperationRepository, EntityManagerInterface $manager)
    {
        $this->compteRepository = $compteRepository;
        $this->typeCompteRepository = $typeCompteRepository;
        $this->typeOperationRepository = $typeOperationRepository;
        $this->manager = $manager;
    }


    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig');
    }
    #[Route('/client/transfert', name: 'client_load_transfert')]
    public function loadTransfert(): Response
    {
        return $this->render('client/transfert.html.twig',[
            'comptes'=>$this->compteRepository->findBy(['client'=>$this->getUser()])
            ]);
    }

    #[Route('/client/transfet/new', name: 'client_transfert_new')]
    public function transfert(Request $request): Response
    {
        $data = $request->request;
        $source = $this->compteRepository->find($data->get('compte'));
        $montant = $data->get('montant');

        if ($source->getType()->getSoldeMin() > ($source->getSolde()-$montant)){
            $this->addFlash('error', 'Montant insuffisant');
            return $this->redirectToRoute('client_load_transfert');
        }

        $receveur = $this->compteRepository->findOneBy(['numero'=>$data->get('receveur')]);
        if (!$receveur->isStatut()){
            $this->addFlash('error', 'Compte bloque');
            return $this->redirectToRoute('client_load_transfert');
        }


        $operation = (new Operation())
            ->setNumero(strtoupper(uniqid()))
            ->setStatut("VALIDE")
            ->setDate(new \DateTime())
            ->setType($this->typeOperationRepository->find(3))
            ->setMontant($montant)
            ->setSource($source)
            ->setReceveur($receveur);

        $receveur->setSolde($receveur->getSolde()+$montant);
        $source->setSolde($source->getSolde() - $montant);

        $this->manager->persist($source);
        $this->manager->persist($receveur);

        $this->manager->persist($operation);
        $this->manager->flush();

        $this->addFlash('done', 'Transfert effectuee');
        return $this->redirectToRoute('client_load_transfert');
    }

    #[Route('/client/info', name: 'client_load_info')]
    public function info(): Response
    {
        return $this->render('client/infos.html.twig', [
            'comptes'=>$this->compteRepository->findBy(['client'=> $this->getUser()])
        ]);
    }

    #[Route('/client/info/{slug}', name: 'client_load_info_dtl')]
    public function loadCompteClient($slug): Response
    {

        $compte = $this->compteRepository->findOneBy(['slug'=>$slug]);
        return $this->render('client/infos.html.twig', [
            'account' => $this->compteRepository->findOneBy(['slug'=>$slug]),
            'comptes'=>$this->compteRepository->findBy(['client'=> $this->getUser()])
        ]);
    }

    #[Route('/client/carte', name: 'client_load_carte')]
    public function carte(): Response
    {

        return $this->render('client/carte.html.twig', [
            'comptes'=>$this->compteRepository->findBy(['client'=> $this->getUser()])
        ]);
    }

}
