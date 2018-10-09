<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
  private $message = 'Le routage Symfony est excellent !';
  private $messages = array(
    'Ruby on Rails est un framework Ruby',
    'Symfony est un framework PHP',
    'Django est un framework Python'
  );

  public function index()
  {
    return new Response('<h1>ok</h1>');
  }

  public function home()
  {
    $html = '<h1>Titre principal</h1>';
    $html .= '<p>Simple paragraphe</p>';

    $res = new Response();
    $res->setContent($html);
    return $res;
  }

  public function bravo()
  {
    return new Response('<p>Message:' . $this->messages[1] .'</p>');
  }

}
