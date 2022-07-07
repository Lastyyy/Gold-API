<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GoldController extends AbstractController
{
    #[Route('/api/gold', name: 'app_gold', methods: [ 'POST' ])]
    public function index(Request $request): JsonResponse
    {
        $request = $request->request->all();
        if(!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', $request["from"]) or
            !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', $request["to"]))
        {
            return $this->json([
                'msg' => "Bad request"
            ], 400);
        }

        else
        {
            $goldUrl = "http://api.nbp.pl/api/cenyzlota/";
            $goldUrl = $goldUrl . substr($request["from"], 0, 10) . '/' . substr($request["to"], 0, 10);

            $apiClient = curl_init($goldUrl);
            curl_setopt($apiClient, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($apiClient);
            $response = json_decode($response, true);

            $from = "";
            $to = "";
            $avg = 0;

            if (!is_null($response)) {
                $from = $response[0]["data"] . "T00:00:00+00:00";
                $to = $response[sizeof($response) - 1]["data"] . "T00:00:00+00:00";
                foreach ($response as $day) {
                    $avg += $day["cena"];
                }
                $avg /= sizeof($response);
                $avg = number_format((float)$avg, 2, '.', '');
            }

            return $this->json([
                'from' => $from,
                'to' => $to,
                'avg' => $avg
            ]);
        }
    }
}
