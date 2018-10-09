<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\User;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function index()
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function delete(SessionInterface $session)
    {
      $session->remove('user'); // suppression de l'utilisateur dans la session
      return $this->redirectToRoute('home');
    }

    /**
     * @Route("/login-process", name="login-process")
     */
    public function process(Request $request, SessionInterface $session)
    {
      $email    = $request->request->get('email');
      $password = $request->request->get('password');
      $userRepo = $this->getDoctrine()->getRepository(User::class);

      // Nous devons récupérer l'utiliseur dont le mot de passe ET
      // l'email correspondant aux valeurs postées
      // les méthodes génériques ->findAll() et ->findBy...
      // sont inadaptées => création et utilisation d'une méthode
      // de recherche personnalisée (dans UserRepository)

      $users = $userRepo
        ->findByEmailAndPassword($email, $password);

      if (count($users) == 0) {
        return new Response(
          'Utilisateur inconnu ou mot de passe erroné');
      } else {
        // utilisateur trouvé
        // enregistrement dans la session
        $session->set('user', $users[0]);

        // return new Response(
        //   $session->get('user')->getFirstname() . ' est connecté');

        // redirection vers la page d'accueil
        return $this->redirectToRoute('home');
      }


    }
}
