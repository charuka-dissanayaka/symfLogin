<?php

namespace App\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\Validate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
class PostController extends Controller
{

    /**
     *@ApiDoc(
     *      resource=true,
     *     description="Get one single post",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The post unique identifier."
     *         }
     *     },
     *     section="posts"
     * )
     * @Route("/api/posts/{id}",name="show_post")
     * @Method({"GET"})
     */
    public function showPost($id)
    {
        $post=$this->getDoctrine()->getRepository('controller:Post')->find($id);


        if (empty($post)){
            $response=array(
                'code'=>1,
                'message'=>'post not found',
                'error'=>null,
                'result'=>null
            );

            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data=$this->get('jms_serializer')->serialize($post,'json');


        $response=array(

            'code'=>0,
            'message'=>'success',
            'errors'=>null,
            'result'=>json_decode($data)

        );

        return new JsonResponse($response,200);


    }


    /**
     * @ApiDoc(
     * description="Create a new post",
     *
     *    statusCodes = {
     *        201 = "Creation with success",
     *        400 = "invalid form"
     *    },
     *    responseMap={
     *         201 = {"class"=Post::class},
     *
     *    },
     *     section="posts"
     *
     *
     * )
     *
     * @param Request $request
     * @param Validate $validate
     * @return JsonResponse
     * @Route("/api/posts",name="create_post")
     * @Method({"POST"})
     */
    public function createPost(Request $request,Validate $validate)
    {

        $data=$request->getContent();

        $post=$this->get('jms_serializer')->deserialize($data,'AppBundle\Entity\Post','json');


        $reponse=$validate->validateRequest($post);

        if (!empty($reponse)){
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }

        $em=$this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();


        $response=array(

            'code'=>0,
            'message'=>'Post created!',
            'errors'=>null,
            'result'=>null

        );

        return new JsonResponse($response,Response::HTTP_CREATED);



    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Get the list of all posts",
     *     section="posts"
     * )
     *
     * @Route("/api/posts",name="list_posts")
     * @Method({"GET"})
     */

    public function listPost()
    {

        $posts=$this->getDoctrine()->getRepository('controller:Post')->findAll();

        if (!count($posts)){
            $response=array(

                'code'=>1,
                'message'=>'No posts found!',
                'errors'=>null,
                'result'=>null

            );


            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }


        $data=$this->get('jms_serializer')->serialize($posts,'json');

        $response=array(

            'code'=>0,
            'message'=>'success',
            'errors'=>null,
            'result'=>json_decode($data)

        );

        return new JsonResponse($response,200);


    }


    /**
     * @Route("/post", name="post")
     */
    
}
