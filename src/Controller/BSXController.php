<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use App\Entity\Stock;
use App\Entity\Option;
use App\Entity\PaperStock;
use App\Entity\Dividend;
use App\Entity\TenPlanWeek;
use App\Entity\WatchStock;
use App\Entity\WatchOption;
use App\Entity\WizardPlay;
use App\Entity\Note;
use App\Form\AddStockType;
use App\Form\AddWatchStockType;
use App\Form\AddWatchOptionType;
use App\Form\AddPaperStockType;
use App\Form\AddOptionType;
use App\Form\BuyStockType;
use App\Form\SellStockType;
use App\Form\SellOptionType;
use App\Form\DividendType;
use App\Form\TenPlanWeekType;
use App\Form\WizardType;
use App\Form\NoteType;
use App\Repository\StockRepository;
use App\Repository\OptionRepository;
use App\Repository\PaperStockRepository;
use App\Repository\DividendRepository;
use App\Repository\TenPlanWeekRepository;
use App\Repository\WizardPlayRepository;
use App\Repository\WatchStockRepository;
use App\Repository\WatchOptionRepository;
use App\Repository\NoteRepository;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

class BSXController extends AbstractController
{
    private $trading_fee = 9.95;
    private $contract_fee = 1.25;
    private $CANtoUSDBuy = 0.8219;
    private $USDtoCANBuy = 1.2517;
    private $CANtoUSDSell = 0.7989;
    private $USDtoCANSell = 1.2167;
    
    /**
     * @Route("/", name="index")
     */
    public function index(StockRepository $stocksRepo, OptionRepository $optionsRepo): Response
    {
        $stocks = $stocksRepo->findAll();
        $options = $optionsRepo->findAll();
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
            'options' => $options,
            'market_open' => $market_open,
        ]);
    }

    /////////////////////// STOCKS ///////////////////////////////

    /**
     * @Route("/updatestockinfo", name="update_stock_info")
     */
    public function updateStockInfo(StockRepository $stocksRepo): Response
    {
        $updateAPIData = true; // Use this a lock..
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
                $stock->setCurrentPrice($MarketStackData['data'][$stockIndex]['close']);
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
        $stock->setBuys(1);
        $stock->setCurrentPrice(0);
        
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

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Add Stock',
            'headline' => 'Add Stock',
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
                $stock->addBuy();
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


        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Buy Stock',
            'headline' => 'Buy Stock',
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

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Sell Stock',
            'headline' => 'Sell Stock',
        ]);
    }

    /**
     * @Route("/update", name="update")
     */
    public function update (StockRepository $Srepo, OptionRepository $Orepo, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $type = $request->get('type');
        $price = $request->get('price');

        if($type === "1"){
            $stock = $Srepo->findOneBy(['id' => $id]);
            $stock->setCurrentPrice((float)$price);
        } 
        
        if($type === "2") {
            $option = $Orepo->findOneBy(['id' => $id]);
            $option->setStockPrice((float)$price);
        }

        if($type === "3") {
            $option = $Orepo->findOneBy(['id' => $id]);
            $option->setCurrentPrice((float)$price);
        }

        $em->flush();

        $response = new Response(json_encode(['success' => 1]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /////////////////// OPTIONS ////////////////////////////

    /**
     * @Route("/addoption", name="add_option")
     */
    public function addOption(Request $request): Response
    {
        $option = new Option();
        
        $form = $this->createForm(AddOptionType::class, $option);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();
            $cost = ($data->getAverage() * 100 * $data->getContracts()) + ($this->contract_fee * $data->getContracts()) + $this->trading_fee;
            $option->setCost($cost);
            
            if($data->getBuyCurrency() === 1)
            {
                if($data->getCurrency() === 1){
                    $option->setProfitCan($cost * -1);
                    $option->setProfitUsd(0);
                }

                if($data->getCurrency() === 2){
                    $option->setProfitCan(($cost * $this->USDtoCANBuy) * -1);
                    $option->setProfitUsd(0);
                }
            }

            if($data->getBuyCurrency() === 2)
            {
                if($data->getCurrency() === 1){
                    $option->setProfitCan(0);
                    $option->setProfitUsd(($cost * $this->CANtoUSDBuy) * -1);
                }

                if($data->getCurrency() === 2){
                    $option->setProfitCan(0);
                    $option->setProfitUsd($cost * -1);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($option);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - S Option',
            'headline' => 'Add Option',
        ]);
    }

    /**
     * @Route("/selloption", name="sell_option")
     */
    public function sellOption(Request $request): Response
    {
        
        $form = $this->createForm(SellOptionType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $data = $form->getData();
                $option = $form->get("ticker")->getData();


                $option->sellContracts($data['contracts']);
                $option->setSold($data['sell_date']);                
                $sell_amount = ($data['contracts'] * $data['price']) * 100;
                $contract_fee_total = $this->contract_fee * $data['contracts'];



                // Adjust Profit
                if($data['sell_currency'] === 1)
                {
                  
                    if($option->getCurrency() === 1){
                        $option->addProfitCan(($sell_amount - $this->trading_fee - $contract_fee_total));
                    }

                    if($option->getCurrency() === 2){
                        $option->addProfitCan((($sell_amount - $this->trading_fee - $contract_fee_total) * $this->USDtoCANSell));
                    }
                    
                }

                if($data['sell_currency'] === 2)
                {
                    if($option->getCurrency() === 1){
                        $option->addProfitUsd((($sell_amount - $this->trading_fee - $contract_fee_total) * $this->CANtoUSDSell));
                    }

                    if($option->getCurrency() === 2){
                        $option->addProfitUsd(($sell_amount - $this->trading_fee - $contract_fee_total));
                    }
                }

                if($option->getContracts() === 0){
                    $option->setAverage(0);
                }

                $em->persist($option);
                $em->flush();

                return $this->redirectToRoute('index');
            }
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Sell Option',
            'headline' => 'Sell Option',
        ]);
    }

    ///////////////// FEATURE PAGES ///////////////////////

    /**
     * @Route("/joint", name="joint")
     */
    public function jointAccountList(StockRepository $stocksRepo): Response
    {
        $stocks = $stocksRepo->findBy(array('type' => array(1, 3)));
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

        if ($currentTime > $opening_bell && $currentTime < $closing_bell){
            $market_open = true;
        }
        return $this->render('bsx/joint.html.twig', [
            'stocks' => $stocks,
            'market_open' => $market_open,
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
     * @Route("/tenplan", name="ten")
     */
    public function tenPlan(TenPlanWeekRepository $tenplanRepo): Response
    {
        $tenplans = $tenplanRepo->findAll();
        
        return $this->render('bsx/tenplan.html.twig', [
            'weeks' => $tenplans
        ]);
    }

    /**
     * @Route("/tenplan/add", name="ten_add")
     */
    public function tenPlanAdd(Request $request): Response
    {
        $week = new TenPlanWeek();
        $form = $this->createForm(TenPlanWeekType::class, $week);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($week);
            $em->flush();

            return $this->redirectToRoute('ten');
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Ten Percent Plan - Add Week',
            'headline' => 'Ten Percent Plan - Add Week',
        ]);
    }

    ///////////////// TICKER WIZARDS ///////////////////////

    /**
     * @Route("/wizards", name="wizards")
     */
    public function wizards(wizardPlayRepository $wizardsRepo): Response
    {
        $wizardPlays = $wizardsRepo->findAll();
        
        return $this->render('bsx/plays.html.twig', [
            'plays' => $wizardPlays
        ]);
    }

    /**
     * @Route("/wizards/add", name="add_wizard_play")
     */
    public function addWizard(Request $request): Response
    {
        $play = new WizardPlay();
        $form = $this->createForm(WizardType::class, $play);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($play);
            $em->flush();

            return $this->redirectToRoute('wizards');
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Add Wizard',
            'headline' => 'Add Wizard Play',
        ]);
    }

    /**
     * @Route("/wizards/delete/{id}", name="wizard_delete", requirements={"id"="\d+"})
     */
    public function deleteWizard(WizardPlay $stock): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($stock);
        $em->flush();
        return $this->redirectToRoute('wizards');
    }

    /**
     * @Route("/wizards/updateplay", name="wizard_update")
     */
    public function updateWizard (WizardPlayRepository $repo, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $postData = json_decode($request->getContent());
        $id = $request->get('id');
        $price = $request->get('price');
        $stock = $repo->findOneBy(['id' => $id]);
        $stock->setCurrentPrice((float)$price);
        $em->flush();

        $response = new Response(json_encode(['success' => 1]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    ///////////////// DIVIDENDS ///////////////////////

    /**
     * @Route("/dividends", name="dividend")
     */
    public function dividend(DividendRepository $dividendRepo): Response
    {
        $dividends = $dividendRepo->findAll();
        return $this->render('bsx/dividends.html.twig', [
            'dividends' => $dividends,
            'controller_name' => 'BSXController',
        ]);
    }

    /**
     * @Route("/dividends/month", name="dividend_month")
     */
    public function dividendMonth(DividendRepository $dividendRepo): Response
    {
        $dividends = $dividendRepo->findAll();
        return $this->render('bsx/dividends_month.html.twig', [
            'dividends' => $dividends,
            'controller_name' => 'BSXController',
        ]);
    }

    /**
     * @Route("/dividends/add", name="add_dividend")
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

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Add Dividend Payment',
            'headline' => 'Add Dividend Payment',
        ]);
    }

    ///////////////// PAPER TRADING ///////////////////////


        /**
     * @Route("/paper", name="paper")
     */
    public function paper(PaperStockRepository $stocksRepo): Response
    {
        $stocks = $stocksRepo->findAll();
        

        return $this->render('bsx/paper.html.twig', [
            'controller_name' => 'BSXController',
            'stocks' => $stocks,
        ]);
    }

    /**
     * @Route("/calculator", name="calculator")
     */
    public function calculator(Request $request): Response
    {
        return $this->render('bsx/calculator.html.twig', []);
    }

     /**
     * @Route("/paper/add", name="paper_add")
     */
    public function addPaper(Request $request): Response
    {
        $stock = new PaperStock();
        $stock->setOpeningPrice(0);
        $stock->setClosingPrice(0);
        $stock->setSold(0);
        $stock->setBuys(1);
        
        $form = $this->createForm(AddPaperStockType::class, $stock);
        
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

            return $this->redirectToRoute('paper');
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Add Paper Stock',
            'headline' => 'Add Paper Stock',
        ]);
    }

    /**
     * @Route("/paper/delete/{id}", name="paper_delete", requirements={"id"="\d+"})
     */
    public function deletePaper(PaperStock $stock): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($stock);
        $em->flush();
        return $this->redirectToRoute('paper');
    }

    ///////////////// WATCHLIST ///////////////////////


    /**
     * @Route("/watchlist", name="watchlist")
     */
    public function watchlist(WatchStockRepository $watchStockRepo, WatchOptionRepository $watchOptionRepo): Response
    {
        $stocks = $watchStockRepo->findAll();
        $options = $watchOptionRepo->findAll();
        
        return $this->render('bsx/watchlist.html.twig', [
            'stocks' => $stocks,
            'options' => $options
        ]);
    }

    /**
     * @Route("/watchlist/addstock", name="watch_stock_add")
     */
    public function addWatchStock(Request $request): Response
    {
        $stock = new WatchStock();
        $form = $this->createForm(AddWatchStockType::class, $stock);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();
            $buy = (float)$data->getBuyin();
            $shares = (float)$data->getShares();
            $pp = $buy + (20 / $shares);
            $precision = 2;

            $stock->setStatus(0);
            $stock->setCurrent(0);
            $stock->setDeadstop(round($buy - ((35 - ($this->trading_fee * 2)) / $shares), $precision));
            $stock->setProfitpoint(round($buy + (20 / $shares), $precision));
            $stock->setBronze(round($pp + (50 / $shares), $precision));
            $stock->setSilver(round($pp + (100 / $shares), $precision));
            $stock->setGold(round($pp + (250 / $shares), $precision));
            $stock->setPlatnum(round($pp + (500 / $shares), $precision));
            $stock->setDiamond(round($pp + (1000 / $shares), $precision));
            $em = $this->getDoctrine()->getManager();
            $em->persist($stock);
            $em->flush();

            return $this->redirectToRoute('watchlist');
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Watch Stock',
            'headline' => 'Watch Stock',
        ]);
    }

     /**
     * @Route("/watchlist/addoption", name="watch_option_add")
     */
    public function addWatchOption(Request $request): Response
    {
        $option = new WatchOption();
        $form = $this->createForm(AddWatchOptionType::class, $option);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();
            $buy = (float)$data->getBuyin();
            $contracts = (float)$data->getContracts();
            $option_buysell_fee = 2 * ((($this->trading_fee / $contracts) / 100) + ($contracts * ($this->contract_fee / 100)));
            $pp = $buy + $option_buysell_fee;
            $precision = 2;

            //var_dump($option_buysell_fee);

            $option->setStatus(0);
            $option->setCurrent(0);
            $option->setDeadstop(round($buy - (0.35 - $option_buysell_fee), $precision));
            $option->setProfit(round($buy + $option_buysell_fee, $precision));
            $option->setBronze(round($pp + (0.50 / $contracts), $precision));
            $option->setSilver(round($pp + (1.00 / $contracts), $precision));
            $option->setGold(round($pp + (2.50 / $contracts), $precision));
            $option->setPlatnum(round($pp + (5.00 / $contracts), $precision));
            $option->setDiamond(round($pp + (10.00 / $contracts), $precision));
            $em = $this->getDoctrine()->getManager();
            $em->persist($option);
            $em->flush();

            return $this->redirectToRoute('watchlist');
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Watch Option',
            'headline' => 'Watch Option',
        ]);
    }

     /**
     * @Route("/watchlist/stock/delete/{id}", name="watch_stock_delete", requirements={"id"="\d+"})
     */
    public function deleteWatchStock(WatchStock $stock): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($stock);
        $em->flush();
        return $this->redirectToRoute('watchlist');
    }

    /**
     * @Route("/watchlist/option/delete/{id}", name="watch_option_delete", requirements={"id"="\d+"})
     */
    public function deleteWatchOption(WatchOption $option): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($option);
        $em->flush();
        return $this->redirectToRoute('watchlist');
    }

    /**
     * @Route("/watchlist/option/profit", name="watch_option_profit")
     */
    public function updateWatchOptionProfit (WatchOptionRepository $repo, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postData = json_decode($request->getContent());
        $id = $request->get('id');
        $dead = $request->get('deadstop');
        $pp = $request->get('pp');
        $target = $request->get('target');
        $golden = $request->get('golden');
        $stock = $repo->findOneBy(['id' => $id]);
        $stock->setDeadstop((float)$dead);
        $stock->setProfit((float)$pp);
        $stock->setTarget((float)$target);
        $stock->setGolden((float)$golden);
        $em->flush();

        $response = new Response(json_encode(['success' => 1]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/watchlist/stock/check", name="watch_stock_check")
     */
    public function watchStockCheck(WatchStockRepository $repo, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $direction_up = 0;

        $postData = json_decode($request->getContent());
        $id = $request->get('id');
        $silent = (int)$request->get('silent');
        $stock = $repo->findOneBy(['id' => $id]);

        $url = $stock->getUrl();
        $name = $stock->getName();
        $ticker = $stock->getTicker();
        $prev = $stock->getCurrent();
        $dead = $stock->getDeadstop();
        $buyin = $stock->getBuyin();
        $profit = $stock->getProfitpoint();
        $bronze = $stock->getBronze();
        $silver = $stock->getSilver();
        $gold = $stock->getGold();
        $platnum = $stock->getPlatnum();
        $diamond = $stock->getDiamond();
        $prev_status = $stock->getStatus();

        /*
        * --- Get Current Price from internet ---
        */

        $content = file_get_contents($url);
        // checks if the content we're receiving isn't empty, to avoid the warning
        if ( empty( $content ) ) {
            return false;
        }

        // converts all special characters to utf-8
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');

        // creating new document
        $doc = new \DOMDocument('1.0', 'utf-8');

        //turning off some errors
        libxml_use_internal_errors(true);

        // it loads the content without adding enclosing html/body tags and also the doctype declaration
        $doc->LoadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $doc->validateOnParse = true;
        $doc->loadHtml($content);
        $xpath = new \DOMXPath($doc);
        $current = (float)substr($xpath->query('//meta[@name="price"]')[0]->attributes[1]->value, 1);

        /*
        * --- Update Status ---
        */ 

        $direction_up = ($current > $prev) ? 1 : 0;

        if($current === $prev){
            $direction_up = 2;
        }


        if($current > $dead){
            $status = 2;
            $range = "Dead Stop Warning (-$20 to -$35)";
            $old_range = (($direction_up === 1) ? 'Dead Stop' : 'Buy In Price');
        } else {
            $status = 1;
            $range = "Below Dead Stop (-$35+)";
            $old_range = "Dead Stop";
        }

        if($current >= $buyin){
            $status = 3;
            $range = "Buy In (-$20 to $0)";
            $old_range = (($direction_up === 1) ? 'Buy In Price' : 'Profit Point');
        }

        if($current >= $profit){
            $status = 4;
            $range = "Small Profit ($0 to $50)";
            $old_range = (($direction_up === 1) ? 'Buy In Price' : 'Bronze Range');
        }

        if($current >= $bronze){
            $status = 5;
            $range = "Bronze ($50 to $100)";
            $old_range = (($direction_up === 1) ? 'Small Profit' : 'Silver Range');
        }

        if($current >= $silver){
            $status = 6;
            $range = "Silver ($100 to $200)";
            $old_range = (($direction_up === 1) ? 'Bronze Range' : 'Gold Range');
        }

        if($current >= $gold){
            $status = 7;
            $range = "Gold ($200 to $500)";
            $old_range = (($direction_up === 1) ? 'Silver Range' : 'Platnum Range');
        }

        if($current >= $platnum){
            $status = 8;
            $range = "Platnum ($500 to $1000)";
            $old_range = (($direction_up === 1) ? 'Gold Range' : 'Diamond Range');
        }

        if($current >= $diamond){
            $status = 9;
            $range = "Diamond ($1000+)";
            $old_range = 'Platnum Range';
        }
       

        if($status !== $prev_status){
            $stock->setStatus($status);
            
            $notifier = NotifierFactory::create();
            
            if($silent !== 1){
            // // Create your notification
            $notification =
                (new Notification())
                ->setTitle($ticker . ' Status Change')
                ->setBody($name . ' stock price is now' . (($direction_up === 1) ? ' above ' : ' below ') . $old_range . '. New Range is '. $range . ' ('. $status .')')
                ->setIcon(__DIR__.'/../../public/images/logos/'. $ticker. '.jpg')
            ;
    
            // // Send it
            $notifier->send($notification);
            //*/
            }
        }


        $stock->setCurrent((float)$current);
        $em->flush();


        $response = new Response(json_encode(['success' => 1, 'id' => $id, 'status' => $status, 'dir_up' => $direction_up, 'url' => $url, 'current' => $current]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/watchlist/option/check", name="watch_option_check")
     */
    public function watchOptionCheck(WatchOptionRepository $repo, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $direction_up = 0;

        $postData = json_decode($request->getContent());
        $id = $request->get('id');
        $silent = (int)$request->get('silent');
        $stock = $repo->findOneBy(['id' => $id]);
        
        $type = ((int)$stock->getType() === 1) ? "c" : "p";
        $expiry_date = $stock->getExpire();
        $name = $stock->getName();
        $ticker = $stock->getTicker();
        $strike = $stock->getStrike();
        $prev = $stock->getCurrent();
        $dead = $stock->getDeadstop();
        $buyin = $stock->getBuyin();
        $profit = $stock->getProfit();
        $bronze = $stock->getBronze();
        $silver = $stock->getSilver();
        $gold = $stock->getGold();
        $platnum = $stock->getPlatnum();
        $diamond = $stock->getDiamond();
        $prev_status = $stock->getStatus();

        /*
        * --- Get Current Price from internet ---
        */

       // Takes raw data from the request
       $option_data = json_decode(file_get_contents('https://www.optionsprofitcalculator.com/ajax/getOptions?stock=' . $ticker . '&reqId=1'), true)['options'][$expiry_date][$type][$strike];
       $current = $option_data['b'];

        /*
        * --- Update Status ---
        */ 

        $direction_up = ($current > $prev) ? 1 : 0;

        if($current === $prev){
            $direction_up = 2;
        }

        if($current > $dead){
            $status = 2;
            $range = "Dead Stop Warning (-$20 to -$35)";
            $old_range = (($direction_up === 1) ? 'Dead Stop' : 'Buy In Price');
        } else {
            $status = 1;
            $range = "Below Dead Stop (-$35+)";
            $old_range = "Dead Stop";
        }

        if($current >= $buyin){
            $status = 3;
            $range = "Buy In (-$20 to $0)";
            $old_range = (($direction_up === 1) ? 'Buy In Price' : 'Profit Point');
        }

        if($current >= $profit){
            $status = 4;
            $range = "Small Profit ($0 to $50)";
            $old_range = (($direction_up === 1) ? 'Buy In Price' : 'Bronze Range');
        }

        if($current >= $bronze){
            $status = 5;
            $range = "Bronze ($50 to $100)";
            $old_range = (($direction_up === 1) ? 'Small Profit' : 'Silver Range');
        }

        if($current >= $silver){
            $status = 6;
            $range = "Silver ($100 to $200)";
            $old_range = (($direction_up === 1) ? 'Bronze Range' : 'Gold Range');
        }

        if($current >= $gold){
            $status = 7;
            $range = "Gold ($200 to $500)";
            $old_range = (($direction_up === 1) ? 'Silver Range' : 'Platnum Range');
        }

        if($current >= $platnum){
            $status = 8;
            $range = "Platnum ($500 to $1000)";
            $old_range = (($direction_up === 1) ? 'Gold Range' : 'Diamond Range');
        }

        if($current >= $diamond){
            $status = 9;
            $range = "Diamond ($1000+)";
            $old_range = 'Platnum Range';
        }

        if($status !== $prev_status){
            $stock->setStatus($status);
            
            if($silent !== 1){
            $notifier = NotifierFactory::create();

            // // Create your notification
            $notification =
                (new Notification())
                ->setTitle($ticker . ' Status Change')
                ->setBody($name . ' stock price is now' . (($direction_up === 1) ? ' above ' : ' below ') . $old_range . '. New Range is '. $range . ' ('. $status .')')
                ->setIcon(__DIR__.'/../../public/images/logos/'. $ticker. '.jpg')
            ;

            // // Send it
            $notifier->send($notification);
            //*/
            }
    
        }


        $stock->setCurrent((float)$current);
        $em->flush();


        $response = new Response(json_encode(['success' => 1, 'id' => $id, 'status' => $status, 'dir_up' => $direction_up, 'current' => $current]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /////////////// NOTES ///////////////////////////

        /**
     * @Route("/notes", name="notes")
     */
    public function notes(NoteRepository $notesRepo): Response
    {
        $notes = $notesRepo->findAll();
        return $this->render('bsx/notes.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/notes/add", name="note_add")
     */
    public function addNote(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $date = new \DateTime();
            $note->setDateCreated($date);
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('notes');
        }

        return $this->render('bsx/form.html.twig', [
            'controller_name' => 'BSXController',
            'form' => $form->createView(),
            'title' => 'BSX - Add Note',
            'headline' => 'Add New Note',
        ]);
    }

    /**
     * @Route("/notes/delete/{id}", name="note_delete", requirements={"id"="\d+"})
     */
    public function deleteNote(Note $note): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($note);
        $em->flush();
        return $this->redirectToRoute('notes');
    }
}



