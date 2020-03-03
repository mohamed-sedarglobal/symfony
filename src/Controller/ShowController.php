<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowController extends AbstractController
{

    /** @Route(
		"/tv_show",
		name="tv_show_schedule",
		methods={"GET","HEAD"}
	)*/
    public function showSchedule(){

        return $this->render('tvshow/show.html.twig', [
            'title' => 'TV Show',
        ]);

    }

    /** @Route(
		"/tv_show_data",
		name="tv_show_schedule_data",
		methods={"GET","HEAD"}
	)*/
    public function showScheduleData(){

        $_todayDate = date('Y-m-d');
        $client = HttpClient::create();
		$response = $client->request('GET', 'http://api.tvmaze.com/schedule?country=US&date='.$_todayDate);

		$statusCode = $response->getStatusCode();
		if (200 !== $response->getStatusCode()) {
    		$return_default =  new JsonResponse([
	            'error_no' => '-200',
	            'error_msg' => 'API Failure',
	            'stories' => [],
	         ]);
		    return $return_default;exit;
		}

		/*if ('application/json' !==  $response->getHeaders()['content-type'][0]){
			$return_default =  new JsonResponse([
	            'error_no' => '-200',
	            'error_msg' => 'API Failure',
	            'stories' => [],
	         ]);
		     return $return_default;exit;
		}*/

		//$content = $response->getContent();
		$content = $response->toArray();
  // //       dump($content);exit;
		// $newArray = array();
		// foreach ($content as $key => $value) {
		// 	$newArray[] = $value;
		// }

		return new JsonResponse([
	            'error_no' => '0',
	            'error_msg' => 'success',
	            'schedule' => $content,
         ]);

    }
}

?>