<?php

namespace ChiarilloMassimo\PokemonGo\Farm\Controller;

use ChiarilloMassimo\PokemonGo\Farm\Form\Type\ConfigType;
use ChiarilloMassimo\PokemonGo\Farm\Service\ConfigManager;
use ChiarilloMassimo\PokemonGo\Farm\SilexApp;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BotController
 * @package ChiarilloMassimo\PokemonGo\Farm\Controller
 */
class BotController extends BaseController
{
    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controllers = parent::connect($app);

        $controllers->get('/new', function() {
           return call_user_func([$this, 'newAction']);
        })->bind('bot_new');

        $controllers->get('/list', function() {
            return call_user_func([$this, 'listAction']);
        })->bind('bot_list');

        $controllers->post('/save', function(Request $request) {
            return call_user_func([$this, 'saveAction'], $request);
        })->bind('bot_save');

        return $controllers;
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        return $this->getApp()['twig']->render('bot/list.html.twig', [
            'configs' => SilexApp::getInstance()['bot.config_manager']->findAll()
        ]);
    }

    /**
     * @return Response
     */
    public function newAction()
    {
        $form = $this->getApp()['form.factory']->create(ConfigType::class);

        return $this->getApp()['twig']->render('bot/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function saveAction(Request $request)
    {
        $form = $this->getApp()['form.factory']->create(ConfigType::class);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->getApp()['twig']->render('bot/new.html.twig', ['form' => $form->createView()]);
        }

        $created = $this->getApp()['bot.config_manager']
            ->build($form->getData());

        if (!$created) {
            $form->addError(new FormError('bot_new.error'));

            return $this->getApp()['twig']->render('bot/new.html.twig', ['form' => $form->createView()]);
        }
    }
}
