<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use Faker;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        // generate data by calling methods
        
        for ($i = 0; $i <= 20; $i++) {
            $product = new Product();
            $product->setName($faker->word());
            $product->setDescription($faker->sentence(15));
            $product->setPrice($faker->numberBetween(99, 99999));
            $product->setStockQty($faker->numberBetween(0, 100));
            $product->setImageUrl($faker->imageUrl(480, 480, 'animals', true));
            $product->setCreatedAt(new \DatetimeImmutable());
            $product->setUpdatedAt(new \DatetimeImmutable());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
