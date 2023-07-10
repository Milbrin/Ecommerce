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
        $imagesUrls = [
            "https://www.etienne-coffeeshop.com/wp-content/uploads/2021/03/mexique-600x600.webp",
            "https://www.etienne-coffeeshop.com/wp-content/uploads/2017/08/450.webp",
            "https://www.etienne-coffeeshop.com/wp-content/uploads/2021/03/MELANGE-GOURMET.webp",
            "https://www.etienne-coffeeshop.com/wp-content/uploads/2017/08/tradition-1907-etienne-coffee-shop-1024x1024.webp"
        ];
        
        for ($i = 0; $i <= 20; $i++) {
            $product = new Product();
            $product->setName($faker->word());
            $product->setDescription($faker->sentence(15));
            $product->setPrice($faker->numberBetween(99, 999));
            $product->setStockQty($faker->numberBetween(0, 100));
            $product->setImageUrl($faker->randomElement($imagesUrls));
            $product->setCreatedAt(new \DatetimeImmutable());
            $product->setUpdatedAt(new \DatetimeImmutable());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
