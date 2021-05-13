<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Stock;
use App\Form\AddStockType;
use App\Form\BuyStockType;
use App\Form\SellStockType;
use App\Repository\StockRepository;

class BSXController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StockRepository $stocksRepo): Response
    {
        
        $stocks = $stocksRepo->findAll();

        

        // forEach($stocks as $stock){
        //     $stock->set = 789; 
        //     $stock['last_bought'] = 123; 
        // }

        // dump($stocks);
        
        /*
        
        // Set API access key 
        $queryString = http_build_query([ 
            'access_key' => '3d5f3feeb237151c7213b3d97ab15237', 
            'symbols' => 'AAPL',
        ]); 
        
        // API URL with query string 
        $apiURL = sprintf('%s?%s', 'http://api.marketstack.com/v1/eod/latest', $queryString); 
        
        // Initialize cURL 
        $ch = curl_init(); 
        
        // Set URL and other appropriate options 
        curl_setopt($ch, CURLOPT_URL, $apiURL); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        
        // Execute and get response from API 
        $api_response = curl_exec($ch); 
        
        // Close cURL 
        curl_close($ch);

        var_dump($api_response);

        */

        return $this->render('bsx/index.html.twig', [
            'controller_name' => 'BSXController',
            'stocks' => $stocks,
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request): Response
    {
        $stock = new Stock();
        $stock->setDividends(0);
        $stock->setSold(0);
        
        $form = $this->createForm(AddStockType::class, $stock);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($stock);
            $em->flush();
        }

        return $this->render('bsx/forms/add.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/buy", name="buy")
     */
    public function buy(Request $request): Response
    {
        $form = $this->createForm(BuyStockType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $data = $form->getData();
                
                $stock = $form->get("ticker")->getData();

                // calculate new average price
                $old_total = $stock->getAveragePrice() *  $stock->getShares();
                $purchase_total = $data['shares'] * $data['price'];
                $new_average = ($old_total + $purchase_total) / ($stock->getShares() + $data['shares']);

                $stock->addShares($data['shares']);
                $stock->setLastBought($data['buy_date']);
                $stock->setAveragePrice(round($new_average, 2, PHP_ROUND_HALF_UP));
                //dump($stock);
            
                $em->persist($stock);
                $em->flush();
        
                return $this->redirectToRoute('index');
            }
        }


        return $this->render('bsx/forms/buy.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sell", name="sell")
     */
    public function sell(Request $request): Response
    {
        $form = $this->createForm(SellStockType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $data = $form->getData();
                $stock = $form->get("ticker")->getData();

                $stock->sellShares($data['shares']);
                $stock->setLastSold($data['sell_date']);

                $stock->addSold($data['price']);
                $stock->addProfit($data['price']);

                if($stock->getShares() === 0){
                    $stock->setAveragePrice(0);
                }

                $em->persist($stock);
                $em->flush();

                return $this->redirectToRoute('index');
            }
        }

        return $this->render('bsx/forms/sell.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/stock", name="stock")
     */
    public function stock(): Response
    {
        return $this->render('bsx/stock.html.twig', [
            'controller_name' => 'BSXController',
        ]);
    }
}
