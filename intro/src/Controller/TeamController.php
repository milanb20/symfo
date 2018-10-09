<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Team;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TeamController extends Controller
{
  public function teamForm(Request $request)
  {
    if ($request->isMethod('post')) {
      // traitement du formulaire

      // récupération des valeurs
      $name           = $request->request->get('name');
      $coach          = $request->request->get('coach');
      $yearFoundation = $request->request->get('yearFoundation');

      // enregistrement en DB
      $team = new Team($name, $coach, $yearFoundation);
      $em = $this->getDoctrine()->getManager();
      $em->persist($team);
      $em->flush();

      // redirection
      return $this->redirectToRoute('teams');
    }

    // GET
    return $this->render('team/form.html.twig');
  }

  public function teamFormBis(Request $request)
  {
    $labels_fr = [
      'name' => 'nom',
      'coach' => 'entraîneur',
      'foundationYear' => 'Année de fondation'
    ];

    $teamExist = false; // flag permettant de savoir si l'équipe
    // qu'on souhaite enregistrer existe déjà en base de données

    // création d'un formulaire d'ajout d'une équipe
    // approche orientée controlleur (!= approche orientée template)
    $team = new Team('','', 2000); // création d'un objet vide
    $form = $this->createFormBuilder($team)
      ->add('name', TextType::class)
      ->add('coach', TextType::class)
      ->add('foundationYear', TextType::class)
      ->add('submit', SubmitType::class, array('label' => 'Enregistrer'))
      ->getForm(); // produit un objet modellisant le formulaire

    // communication entre l'objet form et la requête http
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // isSubmitted() permet de savoir si la formulaire a été posté

      $team = $form->getData(); // l'objet team
      // est alimentée avec les données du formulaire

      // vérifier si existe déjà une équipe ayant le
      // même nom en base de données
      $repo = $this->getDoctrine()->getRepository(Team::class);
      $found  = $repo->findByName($team->getName());

      if (count($found) == 0) {
        // aucune portant déjà ce nom n'a été trouvée
        $em = $this->getDoctrine()->getManager();
        $em->persist($team);
        $em->flush();
        return $this->redirectToRoute('teams');
      } else {
        $teamExist = true;
      }

    }

    return $this->render('team/formbis.html.twig', array(
      'teamExist' => $teamExist,
      'labels_fr' => $labels_fr,
      'form' => $form->createView() // produit le balisage
    ));

  }

  public function teams()
  {
    // récupération des équipes
    $repo = $this->getDoctrine()->getRepository(Team::class);
    $teams = $repo->findAll();

    return $this->render('team/list.html.twig', array(
      'teams' => $teams
    ));
  }
}
