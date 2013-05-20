<?php

namespace My\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use My\BlogBundle\Entity\Post;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/blog")
 * @Template()
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     */
    public function indexAction()
    {
        
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('MyBlogBundle:Post')->findAll();
        return array('posts' => $posts);
    }

    /**
     * showAction 
     * 
     * @param mixed $id 
     * @access public
     * @return void
     * @Route("/show/{id}", name="blog_show")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine')->getManager();
        $post = $em->getRepository('MyBlogBundle:Post')->find($id);
        
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }
        
        return array('post' => $post);
    }    

    /**
     * newAction 
     * 
     * @param Request $request 
     * @access public
     * @return void
     * @Route("/new", name="blog_new")
     */
    public function newAction(Request $request)
    {
        // form build
        $form = $this->createFormBuilder(new Post())
            ->add('title')
            ->add('body')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            // validation
            if ($form->isValid()) {
                // エンティティを永続化
                $post = $form->getData();
                $post->setCreatedAt(new \DateTime());
                $post->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();
                
                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }
    /**
     * editAction 
     * 
     * @param Request $request 
     * @param int $id
     * @access public
     * @return void
     * @Route("/edit/{id}", name="blog_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('MyBlogBundle:Post')->find($id);
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        // form build
        $form = $this->createFormBuilder($post)
            ->add('title')
            ->add('body')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            // validation
            if ($form->isValid()) {
                // エンティティを永続化
                $post = $form->getData();
                $post->setCreatedAt(new \DateTime());
                $post->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                
                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        return array(
            'post' => $post,
            'form' => $form->createView(),
        );
    }
}
