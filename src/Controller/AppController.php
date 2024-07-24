<?php

namespace App\Controller;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class AppController extends AbstractController
{
    #[Route('/stock/{symbol}', name: 'get-stock', methods: ['GET'])]
    public function stock($symbol, EntityManagerInterface $em)
    {
        $symbol = strtoupper($symbol);

        $cache = new FilesystemAdapter();

        /** @var Stock $stock */
        $stock = $cache->get($symbol, function (ItemInterface $item) use ($symbol, $em) {

            echo 'Cache miss!';

            // $item->expiresAfter(10);
            $item->expiresAt(date_create('tomorrow'));

            $repo = $em->getRepository(Stock::class);

            return $repo->findOneBy(['symbol' => $symbol]);
        });

        // $cache->delete($symbol);
        // $cache->clear();

        return new Response("{$stock->getShortName()} has a current value of {$stock->getPrice()}");
    }
}
