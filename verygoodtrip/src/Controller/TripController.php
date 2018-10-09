<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\Picture;
use App\Form\TripType;
use App\Form\PictureType;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trip")
 */
class TripController extends Controller
{
    /**
     * @Route("/", name="trip_index", methods="GET")
     */
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('trip/index.html.twig', ['trips' => $tripRepository->findAll()]);
    }

    /**
     * @Route("/new", name="trip_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($trip);
            $em->flush();

            return $this->redirectToRoute('trip_index');
        }

        //return new Response('test');
        return $this->render('trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="trip_show", methods="GET")
     */
    public function show(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', ['trip' => $trip]);
    }

    /**
     * @Route("/{id}/edit", name="trip_edit", methods="GET|POST")
     */
    public function edit(Request $request, Trip $trip): Response
    {
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        // formulaire photo
        $picture = new Picture(); // création d'un objet vide
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        // si le formulaire de mise à jour d'un voyage a été soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trip_edit', ['id' => $trip->getId()]);
        }

        // si le formulaire d'ajout de photo a été soumis
        if ($formPicture->isSubmitted() && $formPicture->isValid()) {

          // $file = objet de type UploadedFile
          $file = $formPicture->get('path')->getData();

          // récupération du nom du fichier
          $fileName = $file->getClientOriginalName();

          // déplacement
          $file->move($this->getParameter('pictures_folder'), $fileName);

          // remplissage de la propriété path de la photo
          $picture->setPath($fileName);

          // relation entre le voyage et la photo
          $trip->addPicture($picture);

          // enregistrement de l'image en base de données
          $em = $this->getDoctrine()->getManager();
          $em->persist($picture);
          $em->flush();

          return $this->redirectToRoute('trip_edit', ['id' => $trip->getId()]);
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form->createView(),
            'formPicture' => $formPicture->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="trip_delete", methods="DELETE")
     */
    public function delete(Request $request, Trip $trip): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trip);
            $em->flush();
        }

        return $this->redirectToRoute('trip_index');
    }
}
