<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $authors = [];
        for($i=1; $i<=10; $i++) {
            $author = new Author();
            $author->setFirstName('Michel-' . $i);
            $author->setName('Dominique-' . $i);

            $authors[$i] = $author;
            $manager->persist($author);
        }

        $categories = [];
        for($i=1; $i<=10; $i++) {
            $category = new Category();
            $category->setName('Category-' . $i);

            $categories[$i] = $category;
            $manager->persist($category);
        }

        $articles = [];
        for($i=1; $i<=50; $i++) {
            $article = new Article();
            $article->setTitle('Article-' . $i);
            $article->setText('Text-' . $i);
            $article->setCategory($categories[rand(1, sizeof($categories))]);
            $article->setAuthor($authors[rand(1, sizeof($authors))]);

            $articles[$i] = $article;
            $manager->persist($article);
        }

        for($i=1; $i<=200; $i++) {
            $comment = new Comment();
            $comment->setText('Comment-' . $i);
            $comment->setArticle($articles[rand(1, sizeof($articles))]);
            $comment->setAuthor($authors[rand(1, sizeof($authors))]);

            $manager->persist($comment);
        }

        $user = new User();
        $user->setEmail('arthur@gmail.com');
        $user->setRoles([
            'ROLE_ADMIN',
            'ROLE_API'
        ]);
        $user->setPassword($this->encoder->encodePassword($user, 'password'));

        $manager->persist($user);
        $manager->flush();
    }
}
