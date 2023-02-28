<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Compte;
use App\Repository\ClientRepository;
use App\Repository\TypeCompteRepository;
use App\Services\Generator;
use App\Services\MailApi\MailSender;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\TypeResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use function Webmozart\Assert\Tests\StaticAnalysis\upper;

#[isGranted('ROLE_CAISSIER')]
class CaisseCompteController extends AbstractController
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $manager;
    private TypeCompteRepository $typeCompteRepository;
    private ClientRepository $clientRepository;
    private Generator $generator;
    private MailSender $sender;
    public function __construct(
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager,
        TypeCompteRepository $typeCompteRepository,
        ClientRepository $clientRepository,
        Generator $generator,
        MailSender $sender,
    )
    {
        $this->hasher = $hasher;
        $this->manager = $manager;
        $this->typeCompteRepository = $typeCompteRepository;
        $this->generator = $generator;
        $this->clientRepository = $clientRepository;
        $this->sender = $sender;
    }

    #[Route('/caisse/compte', name: 'caisse_compte')]
    public function index(): Response
    {
        return $this->render('caisse_compte/index.html.twig', [
            'types'=>$this->typeCompteRepository->findAll()
        ]);
    }

    #[ Route('/caisse/compte/add', name: 'caisse_add_account', methods: 'POST')    ]
    public function addAccount(Request $request){
        $data = $request->request;
        //dd($data);
        $client = null;
        if ($data->get('newClient')=="y"){
            $client = (new Client())
                ->setNom($data->get("nom"))
                ->setPrenom($data->get("prenom"))
                ->setEmail($data->get("email"))
                ->setIdentifiant(strtoupper(uniqid()))
                ->setAdresse($data->get("adresse"))
                ->setCodePostal($data->get("codePostal"))
                ->setVille($data->get("ville"))
                ->setPays($data->get("pays"))
                ->setNaissance(\DateTime::createFromFormat('Y-m-d', $data->get('naissance')))
                ->setPieceIdentite($data->get('numeroPiece'))
                ->setTypePieceIdentite(strtoupper($data->get("typePiece")))
                ->setExpirationPieceIdentite(\DateTime::createFromFormat('Y-m-d', $data->get('datePiece')));
            $pdwTxt = date_format(new \DateTime(), 'Y-i-s-m-H');
            $client->setPassword($this->hasher->hashPassword($client,$pdwTxt));
            $content = "\n \n Votre compte vient d'etre cree. vos identifiants sont les suivants \n \n Login : ".$client->getEmail()."\n Password : ".$pdwTxt;
            $this->sender->send($client->getEmail(), $client->getPrenom().' '.$client->getNom(), 'Creation de compte', $content);
            $this->manager->persist($client);

        } else {
            $client = $this->clientRepository->findOneBy(['pieceIdentite'=>$data->get('numeroPieceOld')]);
            if ($client == null){
                return $this->redirectToRoute('caisse_compte');
            }
        }

        $type = $this->typeCompteRepository->find($data->get('typeCompte'));
        $compte = (new Compte())
            ->setClient($client)
            ->setOuverture(new \DateTime())
            ->setType($type)
            ->setNumero($this->generator->generateAccountNumber())
            ->setStatut(true)
            ->setSlug(uniqid())
            ->setClient($client);
        if ($type->getCode()=="EP"){
            $compte->setSolde(5000);
        } else {
            $compte->setSolde(15000);
        }

        $this->manager->persist($compte);
        $this->manager->flush();
        return $this->redirectToRoute('app_home');
    }

}
