<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @Route("/blog/{id}/edit", name="blog_edit")
     */

    public function create(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        // méme fonction pour la modification aussi

        if (!$article) {
            $article = new Article();
        }
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


        // $form = $this->createFormBuilder($article)
        // ->add('title', TextType::class, ['attr' => ['placeholder' => "Titre de l'article", 'class' => 'form-control']])
        // ->add('content', TextareaType::class, ['attr' => ['placeholder' => "Contenu de l'article", 'class' => 'form-control']])
        // ->add('image', TextType::class, ['attr' => ['placeholder' => "Image de l'article", 'class' => 'form-control']])

        //autre façon
        // $form = $this->createFormBuilder($article)

        //     ->add('title')
        //     ->add('content')
        //     ->add('image')
        // ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        // ->getForm();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute("blog_show", ['id' => $article->getId()]);

            // dump($article);
        }
        return $this->render('blog/create.html.twig', ['formArticle' => $form->createView(), 'editMode' => $article->getId() !== null]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
