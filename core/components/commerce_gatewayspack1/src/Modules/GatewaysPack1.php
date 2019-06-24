<?php
namespace modmore\Commerce\GatewaysPack1\Modules;
use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Events\Gateways;
use modmore\Commerce\GatewaysPack1\Gateways\AdyenHPP;
use modmore\Commerce\Modules\BaseModule;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Loader\ChainLoader;

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
        $root = dirname(__DIR__, 2);
        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        $dispatcher->addListener(\Commerce::EVENT_GET_PAYMENT_GATEWAYS, [$this, 'registerGateways']);

        // Added in 0.12.0
        if (defined('\Commerce::EVENT_DASHBOARD_LOAD_ABOUT')) {
            $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_LOAD_ABOUT, [$this, 'addLibrariesToAbout']);
        }
    }

    public function registerGateways(Gateways $event)
    {
        $event->addGateway(AdyenHPP::class, 'Adyen');
    }

    public function addLibrariesToAbout(PageEvent $event)
    {
        $lockFile = dirname(dirname(__DIR__)) . '/composer.lock';
        if (file_exists($lockFile)) {
            $section = new SimpleSection($this->commerce);
            $section->addWidget(new ComposerPackages($this->commerce, [
                'lockFile' => $lockFile,
                'heading' => $this->adapter->lexicon('commerce.about.open_source_libraries') . ' - ' . $this->adapter->lexicon('commerce_gatewayspack1'),
                'introduction' => '', // Could add information about how libraries are used, if you'd like
            ]));

            $about = $event->getPage();
            $about->addSection($section);
        }
    }
}
