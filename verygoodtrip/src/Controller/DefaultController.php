<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Country;
use App\Entity\Trip;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $countryRepo = $this->getDoctrine()->getRepository(Country::class);
        $tripRepo = $this->getDoctrine()->getRepository(Trip::class);
        //$countries = $countryRepo->findAll();
        // ->findAll() renvoie les pays ordonnés par id
        // ->findBy permet de filter (arg1), de trier (arg2) et de limiter
        // le nombre de résultats. Exemple :
        // ->findBy(['team' => 'Juventus'], ['birthdate' => 'DESC'], 5)
        // donne les objets (par exemple joueurs) dont la propriété team
        // correspond à Juventus, classés du plus jeune au plus vieux et limités
        // à 5 résultats. Donne les 5 plus jeunes joueurs de la Juventus.
        $countries = $countryRepo->findBy([], ['name' => 'ASC']);
        $trips = null;

        if ($request->isMethod('post')) { // formulaire posté

          // récupération des données postées
          $country_id   = intval($request->request->get('country'));
          $date_start   = $request->request->get('date_start');
          $date_end     = $request->request->get('date_end');
          $price        = floatval($request->request->get('price'));
          $dates        = ['start' => $date_start,  'end' => $date_end];

          // obtention des voyages en fonction des critères sélectionnés
          $trips = $tripRepo->findByCriteria($country_id, $dates, $price);
        }

        return $this->render('default/index.html.twig', array(
          'countries' => $countries,
          'trips' => $trips
        ));
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('default/dashboard.html.twig');
    }
}
