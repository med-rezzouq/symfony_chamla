<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(ArticleRepository $repository)
    {

        // $article = $repository->findOneByTitle('Titre de l\'article');
        // $article = $repository->findByTitle('Titre de l\'article');
        $articles = $repository->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        // dump($request);
        // die;
        // if ($request->request->count() > 0) {
        //     $article = new Article();
        //     $article->setTitle($request->request->get('title'))
        //         ->setContent($request->request->get('content'))
        //         ->setImage($request->request->get('image'))
        //         ->setCreatedAt(new \DateTime());

        //     $manager->persist($article);
        //     $manager->flush();
        //     return $this->redirectToRoute("blog_show", ['id' => $article->getId()]);
        // }

        //we can do instead pour creer le code html formulaire par symfony
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, ['attr' => ['placeholder' => "Titre de l'article", 'class' => 'form-control']])
            ->add('content', TextareaType::class, ['attr' => ['placeholder' => "Contenu de l'article", 'class' => 'form-control']])
            ->add('image', TextType::class, ['attr' => ['placeholder' => "Image de l'article", 'class' => 'form-control']])
            ->getForm();

        return $this->render('blog/create.html.twig', ['formArticle' => $form->createView()]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(ArticleRepository $repository, $id)
    {

        $article = $repository->find($id);


        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }
}
