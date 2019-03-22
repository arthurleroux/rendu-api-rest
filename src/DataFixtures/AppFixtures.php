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
        for($i=0; $i<10; $i++) {
            $author = new Author();
            $author->setFirstName('Michel-' . $i);
            $author->setName('Dominique-' . $i);

            $manager->persist($author);
        }

        for($i=0; $i<10; $i++) {
            $category = new Category();
            $category->setName('Category-' . $i);

            $manager->persist($category);
        }

        for($i=0; $i<50; $i++) {
            $article = new Article();
            $article->setTitle('Article-' . $i);
            $article->setText('Text-' . $i);
            $article->setCategoryId(rand(1, 10));
            $article->setAuthorId(rand(1, 10));

            $manager->persist($article);
        }

        for($i=0; $i<200; $i++) {
            $comment = new Comment();
            $comment->setText('Comment-' . $i);
            $comment->setArticleId(rand(1, 50));
            $comment->setAuthorId(rand(1, 10));

            $manager->persist($comment);
        }

        $user = new User();
        $user->setEmail('arthur@gmail.com');
        $user->setRoles([
            'ROLE_ADMIN'
        ]);
        $user->setPassword($this->encoder->encodePassword($user, 'password'));

        $manager->persist($user);
        $manager->flush();
    }
}
