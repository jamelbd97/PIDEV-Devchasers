<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Entity\Utilisateur;
use App\Form\SocieteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocieteController extends AbstractController
{
    /**
     * @Route("/societe={idSociete}", name="afficherSociete")
     */
    public function afficherSociete($idSociete)
    {
        return $this->render('frontEnd/utilisateur/societe/afficherSociete.html.twig', [
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
        ]);
    }

    /**
     * @Route("/societe", name="afficherToutSociete")
     */
    public function afficherToutSociete()
    {
        return $this->render('frontEnd/utilisateur/societe/afficherToutSociete.html.twig', [
            'societes' => $this->getDoctrine()->getRepository(Societe::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe/ajouter/email={email}/password={motDePasse}", name="ajouterSociete")
     */
    public function ajouterSociete(Request $request, $email, $motDePasse)
    {
        $utilisateur = new Utilisateur();
        $societe = new Societe();

        $form = $this->createForm(SocieteType::class, $societe)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur->setEmail($email)
                ->setMotDePasse($motDePasse)
                ->setTypeUtilisateur(0);

            $societe = $form->getData()
                ->setUtilisateur($utilisateur);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($utilisateur);
            $manager->flush();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($societe);
            $manager->flush();

            return $this->redirectToRoute('afficherUtilisateur');
        }

        return $this->render('frontEnd/utilisateur/societe/manipulerSociete.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Ajouter"
        ]);
    }

    /**
     * @Route("/societe/modifier/id={id}/email={email}/password={motDePasse}", name="modifierSociete")
     */
    public function modifierSociete(Request $request, $id, $email, $motDePasse)
    {
        $manager = $this->getDoctrine()->getManager();
        $utilisateur = $manager->getRepository(Utilisateur::class)->find($id);
        $societe = $manager->getRepository(Societe::class)->find($utilisateur->getSociete()->getID());

        $form = $this->createForm(SocieteType::class, $societe);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setEmail($email)
                ->setMotDePasse($motDePasse)
                ->setTypeUtilisateur(0);

            $societe = $form->getData()
                ->setUtilisateur($utilisateur);

            $manager->persist($utilisateur);
            $manager->persist($societe);
            $manager->flush();

            return $this->redirectToRoute('afficherToutSociete');
        }

        return $this->render('frontEnd/utilisateur/societe/manipulerSociete.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier"
        ]);
    }
}
