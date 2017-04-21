<?php

namespace SelStatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use SelStatsBundle\Form\StatsType;

/**
 * Stats controller.
 *
 * @Route("/stats")
 */
class StatsController extends Controller
{
    /**
     * @Route("/", name="sel_stats_index")
     */
    public function indexAction(Request $request)
    {
        //$um = $this->get('fos_user.user_manager');
        //$users = $um->findUsers();

        if ($request->isXmlHttpRequest()) {
            $period = $request->query->get('stats_filter')['period'];
            return new JsonResponse(array(
                'exchanges' => $this->getExchangesStats($period),
                'services' => $this->getServicesStats($period),
                'users' => $this->getUsersStats($period)
            ));
        }

        $form = $this->createForm('SelStatsBundle\Form\StatsType');

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));
        }

        return $this->render('SelStatsBundle::index.html.twig', array(
            'filter' => $form->createView()
        ));
    }

    protected function getExchangesStats($period) {
        if (!ctype_digit($period)) {
            return array();
        }

        $exchange_data = array(
            "labels" => array(),
            "data1" => array(),
            "data2" => array()
        );

        $em = $this->getDoctrine()->getManager();

        $sql = "
            select DATE_FORMAT(selected_date,\"%Y-%m-%d\") AS _date, COUNT(e.id) as exchanges, COALESCE(SUM(ABS(e.amount)), 0) as amount from 
(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
 LEFT JOIN exchange as e on DATE_FORMAT(e.created,\"%Y-%m-%d\") = DATE_FORMAT(selected_date,\"%Y-%m-%d\")
where selected_date BETWEEN CURDATE() - INTERVAL $period DAY AND CURDATE() + INTERVAL 1 DAY
GROUP BY _date;";

        $q = $em->getConnection()->prepare($sql);
        $q->execute();

        $data = $q->fetchAll();

        foreach($data as $item) {
            $exchange_data["labels"][] = $item['_date'];
            $exchange_data["data1"][] = (int)$item['exchanges'];
            $exchange_data["data2"][] = (float)$item['amount'];
        }

        return $exchange_data;
    }

    protected function getServicesStats($period) {
        if (!ctype_digit($period)) {
            return array();
        }

        $service_data = array(
            "labels" => array(),
            "offre" => array(),
            "demande" => array(),
            "offre_flash" => array(),
            "demande_flash" => array()
        );

        $em = $this->getDoctrine()->getManager();

        $sql = "
            select DATE_FORMAT(selected_date,\"%Y-%m-%d\") AS _date, 
                SUM(CASE WHEN s.type = 1 THEN 1 ELSE 0 END) AS offre, 
                SUM(CASE WHEN s.type = 2 THEN 1 ELSE 0 END) AS demande, 
                SUM(CASE WHEN s.type = 3 THEN 1 ELSE 0 END) AS offre_flash, 
                SUM(CASE WHEN s.type = 4 THEN 1 ELSE 0 END) AS demande_flash from 
(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
 LEFT JOIN service as s on DATE_FORMAT(s.created,\"%Y-%m-%d\") = DATE_FORMAT(selected_date,\"%Y-%m-%d\")
where selected_date BETWEEN CURDATE() - INTERVAL $period DAY AND CURDATE() + INTERVAL 1 DAY
GROUP BY _date";

        $q = $em->getConnection()->prepare($sql);
        $q->execute();

        $data = $q->fetchAll();

        foreach($data as $item) {
            $service_data["labels"][] = $item['_date'];
            $service_data["offre"][] = (int)$item['offre'];
            $service_data["demande"][] = (int)$item['demande'];
            $service_data["offre_flash"][] = (int)$item['offre_flash'];
            $service_data["demande_flash"][] = (int)$item['demande_flash'];
        }


        return $service_data;
    }

    protected function getUsersStats($period) {
        if (!ctype_digit($period)) {
            return array();
        }
        $user_data = array(
            "labels" => array(),
            "user" => array()
        );

        $em = $this->getDoctrine()->getManager();

        $sql = "
            select DATE_FORMAT(selected_date,\"%Y-%m-%d\") AS _date, COUNT(db_user.id) as user from 
(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
 LEFT JOIN db_user on DATE_FORMAT(db_user.created,\"%Y-%m-%d\") = DATE_FORMAT(selected_date,\"%Y-%m-%d\")
where selected_date BETWEEN CURDATE() - INTERVAL $period DAY AND CURDATE() + INTERVAL 1 DAY
GROUP BY _date";

        $q = $em->getConnection()->prepare($sql);
        $q->execute();

        $data = $q->fetchAll();

        foreach($data as $item) {
            $user_data["labels"][] = $item['_date'];
            $user_data["user"][] = (int)$item['user'];
        }


        return $user_data;
    }
}
