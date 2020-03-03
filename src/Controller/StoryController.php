<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StoryController extends AbstractController
{

    /** @Route(
		"/top_story",
		name="top_stories",
		methods={"GET","HEAD"}
	)*/
    public function topStoryPage(){
        $colHeader = [
        	'ID',
        	'Title',
        	'Url',
        	'Comment count',
        	'Score',
        	'Date',
        	'Posted by',
        ];


        return $this->render('news/main.html.twig', [
            'title' => 'Top Story',
            'colHeader' => $colHeader,
        ]);
    }

    /** @Route(
		"/top_content",
		name="top_contents",
		methods={"GET","POST","HEAD"}
	)*/
    public function topStoryAjax(){

    	$request = new Request();
    	$_filter = $request->get('sort_by');
    	$_filter_data = 'search?';
    	$cameInside = FALSE;
    	if($_filter == 'popular'){
    		$_filter_data = 'search?';
    	}else if($_filter == 'more_recent'){
    		$_filter_data = 'search_by_date?';
    	}

    	$_tags = $request->get('search_by');
    	$_tags_data = '';

    	if($_tags == 'story'){
    		$_tags_data = 'tags=story';
    	}else if($_tags == 'comment'){
    		$_tags_data = 'tags=comment';
    	}else if($_tags == 'poll'){
    		$_tags_data = 'tags=poll';
    	}

    	$_searchFor = $request->get('search_for');
    	$_searchFor_data = '';
    	if($_searchFor != ''){
    		$_searchFor_data = '&query='.$_searchFor;
    	}

    	
    	$_per_page_data = '';
    	$_per_page = $request->get('data_count');
    	if($_per_page !=""){
    		$_per_page_data = '&hitsPerPage='.$_per_page;
    	}
    	$_page_no = 1;
    	$_path = $_filter_data.$_tags_data.$_per_page_data.$_searchFor_data;
    	//tags=story&=points>100&page=1&hitsPerPage=10
    	 //dump($_path);exit;
        $client = HttpClient::create();
		$response = $client->request('GET', 'http://hn.algolia.com/api/v1/'.$_path);

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
		$newArray = array();
		foreach ($content['hits'] as $key => $value) {
			//dump($value['created_at_i']);exit;
			$createdTime = $value['created_at_i'];
			$value['created_at_i'] = date("d-M-Y", $createdTime);
			$newArray[] = $value;
		}

         return new JsonResponse([
	            'error_no' => '0',
	            'error_msg' => 'success',
	            'stories' => $newArray,
         ]);

    }
}

?>