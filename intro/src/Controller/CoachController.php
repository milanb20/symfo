<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/coach")
 */
class CoachController extends Controller
{
    /**
     * @Route("/", name="coach_index", methods="GET")
     */
    public function index(CoachRepository $coachRepository): Response
    {
        return $this->render('coach/index.html.twig', ['coaches' => $coachRepository->findAll()]);
    }

    /**
     * @Route("/new", name="coach_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $coach = new Coach();
        $form = $this->createForm(CoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($coach);
            $em->flush();

            return $this->redirectToRoute('coach_index');
        }

        return $this->render('coach/new.html.twig', [
            'coach' => $coach,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="coach_show", methods="GET")
     */
    public function show(Coach $coach): Response
    {
        return $this->render('coach/show.html.twig', ['coach' => $coach]);
    }

    /**
     * @Route("/{id}/edit", name="coach_edit", methods="GET|POST")
     */
    public function edit(Request $request, Coach $coach): Response
    {
        $form = $this->createForm(CoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('coach_edit', ['id' => $coach->getId()]);
        }

        return $this->render('coach/edit.html.twig', [
            'coach' => $coach,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="coach_delete", methods="DELETE")
     */
    public function delete(Request $request, Coach $coach): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coach->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($coach);
            $em->flush();
        }

        return $this->redirectToRoute('coach_index');
    }
}
