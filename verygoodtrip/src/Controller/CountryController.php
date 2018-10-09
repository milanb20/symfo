<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/country")
 */
class CountryController extends Controller
{
    /**
     * @Route("/", name="country_index", methods="GET")
     */
    public function index(CountryRepository $countryRepository): Response
    {
        return $this->render('country/index.html.twig', ['countries' => $countryRepository->findAll()]);
    }

    /**
     * @Route("/new", name="country_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($country);
            $em->flush();

            return $this->redirectToRoute('country_index');
        }

        return $this->render('country/new.html.twig', [
            'country' => $country,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="country_show", methods="GET")
     */
    public function show(Country $country): Response
    {
        return $this->render('country/show.html.twig', ['country' => $country]);
    }

    /**
     * @Route("/{id}/edit", name="country_edit", methods="GET|POST")
     */
    public function edit(Request $request, Country $country): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('country_edit', ['id' => $country->getId()]);
        }

        return $this->render('country/edit.html.twig', [
            'country' => $country,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="country_delete", methods="DELETE")
     */
    public function delete(Request $request, Country $country): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($country);
            $em->flush();
        }

        return $this->redirectToRoute('country_index');
    }
}
