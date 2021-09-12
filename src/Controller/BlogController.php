<?php


namespace App\Controller;


use App\Entity\BlogPost;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * Class BlogController
 * @Route("/blog")
 */
class BlogController extends  AbstractController
{

  /**
   * @Route("/{page}", name="blog_list", defaults={"page":1}, requirements={"page"="\d+"})
   * 
   */
  public function list($page = 1, Request $request)
  {
    $limit = $request->get('limit', 10);
    $repository = $this->getDoctrine()->getRepository(BlogPost::class);
    $item = $repository->findAll();
    return $this->json([
      'page' => $page,
      'limit' => $limit,
      'data' => array_map(function (BlogPost $item) {
        return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
      }, $item)
    ]);
  }
  /**
   * @Route("/post/{id}", name="blog_id", requirements={"id"="\d+"}, methods={"GET"})
   *@ParamConverter("post", class="App:BlogPost")
   */
  public function post($post)
  {
       //return $this->json($this->getDoctrine()->getRepository(BlogPost::class)->find($id));
      //It's the same as doing find($id) on repository
    return $this->json($post);
  }
  /**
   * @Route("/post/{slug}", name="blog_by_slug" ,methods={"GET"})
   * The below annotation is not required when $post is typeHinted with BlogPost
   * and oute parameter name matches any field on the BlogPost entity
   * @ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
   */
  public function postBySlug( BlogPost $post)
  {
    //return $this->json($this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug' => $slug]));
      //Same as doing findOneBy(['slug' => $slug])
      return $this->json($post);
  }

    /**
     * @Route("/add", name="blog_add", methods={"POST"} )
     */
  public function add(Request $request)
  {
      /** @var Serializer $serializer */
      $serializer = $this->get('serializer');
      $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json' );
      $em = $this->getDoctrine()->getManager();
      $em->persist($blogPost);
      $em->flush();

      return $this->json($blogPost);

  }

    /**
     * @Route("/post/{id}", name="blog_delete", methods={"DELETE"} )
     */
  public function delete(BlogPost $post)
  {
      $em = $this->getDoctrine()->getManager();
      $em->remove($post);
      $em->flush();
      return new JsonResponse(null, 204);

  }
}
