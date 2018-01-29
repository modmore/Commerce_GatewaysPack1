<?php
namespace modmore\Commerce\GatewaysPack1\Modules;
use modmore\Commerce\Events\Gateways;
use modmore\Commerce\GatewaysPack1\Gateways\AdyenHPP;
use modmore\Commerce\Modules\BaseModule;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class GatewaysPack1 extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_gatewayspack1:default');
        return $this->adapter->lexicon('commerce_gatewayspack1');
    }

    public function getAuthor()
    {
        return 'modmore';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_gatewayspack1.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_gatewayspack1:default');

        // Add template path to twig
        /** @var ChainLoader $loader */
        $root = dirname(dirname(__DIR__));
        $loader = $this->commerce->twig->getLoader();
        $loader->addLoader(new FilesystemLoader($root . '/templates/'));

        $dispatcher->addListener(\Commerce::EVENT_GET_PAYMENT_GATEWAYS, [$this, 'registerGateways']);
    }

    public function registerGateways(Gateways $event)
    {
        $event->addGateway(AdyenHPP::class, 'Adyen');
    }
}
