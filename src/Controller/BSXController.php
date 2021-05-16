<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Stock;
use App\Entity\Dividend;
use App\Form\AddStockType;
use App\Form\BuyStockType;
use App\Form\SellStockType;
use App\Form\DividendType;
use App\Repository\StockRepository;

class BSXController extends AbstractController
{
    private $trading_fee = 9.95;
    private $CANtoUSDBuy = 0.8378;
    private $USDtoCANBuy = 1.2286;
    private $CANtoUSDSell = 0.8139;
    private $USDtoCANSell = 1.1936;
    
    /**
     * @Route("/", name="index")
     */
    public function index(StockRepository $stocksRepo): Response
    {
        $stocks = $stocksRepo->findAll();
        $market_open = false;

        date_default_timezone_set('America/New_York');

        $currentTime = new \DateTime();
        $opening_bell = \DateTime::createFromFormat('h:i a', "9:30 am");
        $closing_bell = \DateTime::createFromFormat('h:i a', "4:00 pm");

        $data = [
            "CT" => $currentTime,
            "OB" => $opening_bell,
            "CB" => $closing_bell
        ];

        //dump($data);

        if ($currentTime > $opening_bell && $currentTime < $closing_bell){
            $market_open = true;
        }

        $dayoftheweek = date("l", $currentTime->getTimestamp());
        $dayoftheweek = strtolower($dayoftheweek);
        
        if($dayoftheweek == "saturday" || $dayoftheweek == "sunday") {
            $market_open = false;
        }

        return $this->render('bsx/index.html.twig', [
            'controller_name' => 'BSXController',
            'stocks' => $stocks,
            'market_open' => $market_open,
        ]);
    }

    /**
     * @Route("/updatestockinfo", name="update_stock_info")
     */
    public function updateStockInfo(StockRepository $stocksRepo): Response
    {
        $updateAPIData = false;
        $stocks = $stocksRepo->findAll();

        forEach($stocks as $stock){
            $tickers[] = $stock->getTicker();
        }

        if ($updateAPIData) {

            ///*
            // Set API access key 
            $queryString = http_build_query([ 
                'access_key' => '3d5f3feeb237151c7213b3d97ab15237', 
                'symbols' => implode(",", $tickers),
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
            
            $MarketStackData = json_decode($api_response, true);
            $stockIndex = 0;
            //*/

            forEach($stocks as $stock){
                $stock->setOpeningPrice($MarketStackData['data'][$stockIndex]['open']);
                $stock->setClosingPrice($MarketStackData['data'][$stockIndex]['close']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($stock);
                $em->flush();
                $stockIndex++;
            }
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request): Response
    {
        $stock = new Stock();
        $stock->setOpeningPrice(0);
        $stock->setClosingPrice(0);
        $stock->setSold(0);
        
        $form = $this->createForm(AddStockType::class, $stock);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();
            if($data->getBuyCurrency() === 1)
            {
                if($data->getCurrency() === 1){
                    $stock->setProfitCan((($data->getShares() * $data->getAveragePrice()) + $this->trading_fee) * -1);
                    $stock->setProfitUsd(0);
                }

                if($data->getCurrency() === 2){
                    $stock->setProfitCan(((($data->getShares() * $data->getAveragePrice()) + $this->trading_fee) * $this->USDtoCANBuy) * -1);
                    $stock->setProfitUsd(0);
                }
                
            }

            if($data->getBuyCurrency() === 2)
            {
                if($data->getCurrency() === 1){
                    $stock->setProfitCan(0);
                    $stock->setProfitUsd(((($data->getShares() * $data->getAveragePrice()) + $this->trading_fee) * $this->CANtoUSDBuy) * -1);
                }

                if($data->getCurrency() === 2){
                    $stock->setProfitCan(0);
                    $stock->setProfitUsd((($data->getShares() * $data->getAveragePrice()) + $this->trading_fee) * -1);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($stock);
            $em->flush();

            return $this->redirectToRoute('index');
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

                // Calculate new average price
                $old_total = $stock->getAveragePrice() *  $stock->getShares();
                $purchase_total = $data['shares'] * $data['price'];
                $new_average = ($old_total + $purchase_total) / ($stock->getShares() + $data['shares']);

                $stock->addShares($data['shares']);
                $stock->setLastBought($data['buy_date']);
                $stock->setAveragePrice(round($new_average, 2, PHP_ROUND_HALF_UP));
                //dump($stock);

                // Adjust Profit
                if($data['buy_currency'] === 1)
                {
                  
                    if($stock->getCurrency() === 1){
                        $stock->addProfitCan(($purchase_total + $this->trading_fee) * -1);
                    }

                    if($stock->getCurrency() === 2){
                        $stock->addProfitCan((($purchase_total + $this->trading_fee) * $this->USDtoCANBuy) * -1);
                    }
                    
                }

                if($data['buy_currency'] === 2)
                {
                    if($stock->getCurrency() === 1){
                        $stock->addProfitUsd((($purchase_total + $this->trading_fee) * $this->CANtoUSDBuy) * -1);
                    }

                    if($stock->getCurrency() === 2){
                        $stock->addProfitUsd(($purchase_total + $this->trading_fee) * -1);
                    }
                }
                
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
                $sell_amount = $data['shares'] * $data['price'];


                // Adjust Profit
                if($data['sell_currency'] === 1)
                {
                  
                    if($stock->getCurrency() === 1){
                        $stock->addProfitCan(($sell_amount - $this->trading_fee));
                    }

                    if($stock->getCurrency() === 2){
                        $stock->addProfitCan((($sell_amount - $this->trading_fee) * $this->USDtoCANSell));
                    }
                    
                }

                if($data['sell_currency'] === 2)
                {
                    if($stock->getCurrency() === 1){
                        $stock->addProfitUsd((($sell_amount - $this->trading_fee) * $this->CANtoUSDSell));
                    }

                    if($stock->getCurrency() === 2){
                        $stock->addProfitUsd(($sell_amount - $this->trading_fee));
                    }
                }

                $stock->addSold(($sell_amount - $this->trading_fee));

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

    /**
     * @Route("/dividend", name="dividend")
     */
    public function dividend(Request $request): Response
    {
        $dividend = new Dividend();
        $form = $this->createForm(DividendType::class, $dividend);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($dividend);
            $em->flush();
        }

        return $this->render('bsx/dividend.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dividend/add", name="add_dividend")
     */
    public function addDividend(Request $request): Response
    {
        $dividend = new Dividend();
        $form = $this->createForm(DividendType::class, $dividend);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($dividend);
            $em->flush();

            //return $this->redirectToRoute('index');
        }

        return $this->render('bsx/forms/dividend.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
        ]);
    }
}
