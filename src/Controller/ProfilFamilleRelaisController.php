<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Chien;
use App\Entity\User;
use App\Form\ProfilFamilleType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilFamilleRelaisController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository){
        $this -> entityManager = $entityManager;
        $this -> userRepository = $userRepository;
    }
    /**
     * @Route("/profil-famille-relais/{id}/", name="profil_famille_relais")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->get('id');
        $fam = $this->entityManager->getRepository(User::class)->findById($id);

        $repository = $em->getRepository(Booking::class);
        $bookings = $repository->findAll();

        $repositoryChien = $em->getRepository(Chien::class);
        $chiens = $repositoryChien->findAll();


        return $this->render('famille_relais/profil.html.twig', [
            'controller_name' => 'ProfilFamilleRelaisController',
            'famille' => $fam,
            'bookings' => $bookings,
            'chiens' => $chiens,
        ]);
    }

    /**
     * @Route("/profil-famille-relais/{id}/edit", name="edit_profil_famille_relais")
     */
    public function edit(User $profil, UserPasswordHasherInterface $passwordEncoder, Request $request): Response {
        $form = $this->createForm(ProfilFamilleType::class, $profil);
        $id = $request->get('id');
        $fam = $this->entityManager->getRepository(User::class)->findById($id);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $profil = $form->getData();

            $this->entityManager->persist($profil);
            $this->entityManager->flush();

            $this->addFlash('success', "La modification a bien été prise en compte.");

            return $this->redirectToRoute('profil_famille_relais', ['id' => $profil->getId()]);
        }

        return $this->render('famille_relais/edit.html.twig',
            ['form' => $form->createView(), 'famille' => $fam],

        );
    }
    /**
     * @Route("/profil-famille-relais/{id}/suppression", name="delete_profil_famille_relais", methods={"POST", "GET"})
     *
     */
    public function deleteUser(User $familly, Request $request): Response
    {
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $usrRepo = $em->getRepository(User::class);
        $em->remove($familly);
        $em->flush();

        $this->addFlash('success', "La suppression a bien été prise en compte.");

        return $this->redirectToRoute('famille_relais');

    }
}
