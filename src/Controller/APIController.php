<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DonneeDuJourRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api", name="api_")
 */
class APIController extends AbstractController
{
    /**
     * @Route("/dj/liste", name="dj_liste", methods = {"GET"})
     */
    public function index(DonneeDuJourRepository $repoDj, EntityManagerInterface $em): Response
    {
        $donnees = $repoDj->apiFindAll($em);
        // on encode les donnes en json 
        $encoders = [new JsonEncoder()];
        // on a besoin de l'Objectnormaliser pour normaliser l'objet collection en tableau
        $normalizers = [new ObjectNormalizer()];
        // on instancie l'outils serializer qui fera la transcription de donnée

        $serialiser = new Serializer($normalizers, $encoders);
        // on serialize les $donnees
        $dataJson = $serialiser->serialize($donnees, 'json', [
            'circular_reference_handler' => function($object){
                return $object->getId();
            }
        ]);
        $response = new Response($dataJson);
        // asiana header le response (asianaentete http)
        $response->headers->set('Content-Type', 'application/json');
      return $response;
    }
}
