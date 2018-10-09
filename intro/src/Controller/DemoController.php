<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Player;
use App\Entity\Team;

class DemoController extends Controller
{
  public $players = [
    array(
      'id' => 1,
      'name' => 'Pogba',
      'num' => 6,
      'substitute' => false,
      'photo' => 'http://cdn.sports.fr//images/media/football/coupe-du-monde-2018/equipe-de-france/articles/pogba-le-patron-a-parle/pogba/25738259-1-fre-FR/Pogba_w484.jpg'
    ),
    array(
      'id' => 2,
      'name' => 'Lloris',
      'num' => 1,
      'substitute' => true,
      'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Lloris_2018_%28cropped%29.jpg/260px-Lloris_2018_%28cropped%29.jpg'
    ),
    array(
      'id' => 3,
      'name' => 'Mbappé',
      'num' => 10,
      'substitute' => false,
      'photo' => 'https://le10static.com/img/a/2/6/5/6/2/4/265624-large.jpg'
    )
  ];

  public function index()
  {
    $colors = ['bleu', 'blanc', 'rouge'];

    // la méthode render() provient de la classe Controller
    return $this->render('demo.html.twig', array(
      'title' => 'Demo TWIG',
      'message' => 'Simple message en provenance du controller',
      'available' => true,
      'colors' => $colors,
      'players' => $this->players
    ));
  }

  public function player($id)
  {
    // $id correspond au paramètre d'url {id} défini
    // dans le fichier de routage

    // à partir de l'identifiant, on va récupérer la
    // totalité des données concernant le joueur identifié

    $player = null;
    foreach($this->players as $p) {
      // joueur trouvé dans la source de données
      if ($p['id'] == $id) {
        $player = $p;
      }
    }

    return $this->render('player.html.twig', array(
      'player' => $player
    ));
  }

  public function players()
  {
    $p1 = new Player('Abdel M', 10, false, '');
    $p2 = new Player('Cristiano Ronaldo', 7, false, '');
    $p3 = new Player('Adil Rami', 17, true, '');

    //$this->addPlayers();

    // récupérer les joueurs en base de données
    // pour les requêtes en lecture, on utilise getRepository()
    // le Repository gère toutes les requêtes en lecture
    $repo = $this->getDoctrine()->getRepository(Player::class);
    $players = $repo->findAll(); // SELECT * FROM Player

    return $this->render('players.html.twig', array(
      'title' => 'Liste de joueurs',
      'players' => [$p1, $p2, $p3],
      'players2' => $players
    ));

  }

  private function addPlayers()
  {
    $p1 = new Player('Pogba', 6, false, 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Lloris_2018_%28cropped%29.jpg/260px-Lloris_2018_%28cropped%29.jpg');
    $p2 = new Player('Lloris', 1, true, 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Lloris_2018_%28cropped%29.jpg/260px-Lloris_2018_%28cropped%29.jpg');

    // getManager() fournit un objet gérant
    // les requêtes en écriture
    $em = $this->getDoctrine()->getManager();
    $em->persist($p1); // requête pendante (en attente d'exécution)
    $em->persist($p2); // requête pendante (en attente d'exécution)
    $em->flush(); // exécutions des requêtes pendantes
  }

  public function playerForm(Request $request)
  {
    // l'objet $request permet d'obtenir des informations
    // sur le requête

    $teamRepo = $this->getDoctrine()->getRepository(Team::class);

    // 2 cas de figure

    // Cas 1 : POST => traitement de l'envoi du formulaire
    if ($request->isMethod('post')) {
      // si la méthode HTTP de la requête est post
      // récupérer les valeurs postées
      //var_dump($request->request)
      //$request->request version objet de $_POST
      //var_dump($_POST);
      $name       = $request->request->get('name');
      $num        = $request->request->get('num');
      $substitute = $request->request->get('substitute');
      $photo      = $request->request->get('photo');
      $team_id    = intval($request->request->get('team'));

      //var_dump($substitute);
      //renvoie NULL si checkbox non cochée, si cochée : "On"

      // reformatage des données afin qu'elles correspondent
      // au type attendu (bool) par le constructreur de la classe Player
      if ($substitute) {
        $substitute = true;
      } else {
        $substitute = false;
      }

      // Enregistrement en DB
      $player = new Player($name, $num, $substitute, $photo);

      //$player->setTeam($team_id); // INTERDIT
      // setTeams attend un argument de type Team (un objet) pas un entier
      // solution: obtenir un objet à partir de l'identifiant (entier)
      // findById renvoie un tableau d'objets, on extrait l'objet
      // situé à l'indice 0
      $team = $teamRepo->findById($team_id)[0];
      $player->setTeam($team);

      $em = $this->getDoctrine()->getManager();
      $em->persist($player);
      $em->flush();

      // redirection vers la route players
      return $this->redirectToRoute('players');
    }

    // Cas 2: GET => retourne le formulaire
    return $this->render('playerForm.html.twig', array(
      'teams' => $teamRepo->findAll() // récupération des équipes
    ));
  }
}
