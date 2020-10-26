<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->name);
            $product->setSku($faker->isbn13);
            $product->setCategory($faker->word);
            $product->setBrand($faker->company);
            $product->setWeight($faker->randomFloat(2));
            $product->setLength($faker->randomFloat(2));
            $product->setHeight($faker->randomFloat(2));
            $product->setWidth($faker->randomFloat(2));
            $product->setImages([
                $faker->imageUrl($width = 640, $height = 480),
                $faker->imageUrl($width = 640, $height = 480),
                $faker->imageUrl($width = 640, $height = 480),
                ]);
            $product->setStock($faker->randomNumber());
            $product->setPrice($faker->randomFloat(2));
            $product->setDiscountPrice($faker->randomFloat(2));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
