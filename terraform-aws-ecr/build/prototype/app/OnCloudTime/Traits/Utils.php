<?php

namespace App\Reports\Traits;

use Symfony\Component\HttpFoundation\Response;

trait Utils {

   public function formatDate($date) {
        $formatedDate = date('Y-m-d', strtotime($date));
        return $formatedDate;
    }

    public function requestHeaders($request) {
        $from_date = $request->request->get("from_date");
        $to_date = $request->request->get("to_date");
        $supplier = $request->request->get("supplier");
        $journals = $request->request->get("journals");
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        $type = $request->request->get("type");
        $params = [
            'from_date' => $this->formatDate($from_date)." 00:00:00",
            'to_date' => $this->formatDate($to_date)." 23:59:59",
            'supplier' => $supplier,
	        'journals' => $journals,
            'username' => $username,
            'password' => $password,
            'type' => $type
        ];

        return $params;
    }

    public function response($response, $encode = true) {
       $headers['Access-Control-Allow-Methods'] = 'POST, GET, OPTIONS';
        $headers['Access-Control-Allow-Headers'] = 'x-requested-with, x-requested-by, content-type';
        $headers['Content-Type'] = 'application/json';
        if ($encode) {

            return new Response(json_encode($response), 200, $headers);
        }
        //var_dump($response);
        return new Response($response);
    }

    public function errorResponse($response, $encode = true) {
       $headers['Access-Control-Allow-Methods'] = 'POST, GET, OPTIONS';
        $headers['Access-Control-Allow-Headers'] = 'x-requested-with, x-requested-by, content-type';
            $headers['Content-Type'] = 'application/json';
        if ($encode) {

            return new Response(json_encode($response), 500, $headers);
        }
        //var_dump($response);
        return new Response($response);
    }

    public function render($response, $view) {
      return $this->context['twig']->render($view, $response);
    }

    public function renderView($response, $view) {
        return $this->context['twig']->renderView($view, $response);
    }

    public function getPercentage($value, $total) {
        $percentage = number_format(($value / $total) * 100, 2);

        return $percentage;
    }

}
