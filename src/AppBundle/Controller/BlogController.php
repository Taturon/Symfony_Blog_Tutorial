<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/blog")
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findAll();

        return $this->render('blog/index.html.twig', [
           'posts' => $posts
        ]);
    }

    /**
     * @Route("/{id}", name="blog_show", requirements={"id"="\d+"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        return $this->render('blog/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/new", name="blog_new")
     */
    public function newAction(Request $request)
    {
        // フォームの組立
        $form = $this->createFormBuilder(new Post())
            ->add('title')
            ->add('content')
            ->getForm();

        // POST判定&バリデーション
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // エンティティを永続化
            $post->setCreatedAt(new \DateTime());
            $post->setUpdatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="blog_edit", requirements={"id"="\d+"})
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }

        $form = $this->createFormBuilder($post)
            ->add('title')
            ->add('content')
            ->getForm();
    }

    /**
     * @Route("/{id}/delete", name="blog_delete", requirements={"id"="\d+"})
     */
    function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }
        // 削除
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('blog_index');
    }
}
