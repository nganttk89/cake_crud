<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Log\Log;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Auth', [
            'storage' => 'Memory',
            'loginRedirect' => [
                'controller' => 'User',
                'action' => 'index'
            ],
            'authenticate' => [
                'Form' => [
                    'userModel' => 'User',
//                    'finder' => 'auth',
                    'fields' => [
                        'username' => 'name',
                        'password' => 'password'
                    ],
                    'passwordHasher' => [
                        'className' => 'Default'
                    ]
                ],
                'ADmad/JwtAuth.Jwt' => [
                    'userModel' => 'User',
                    'header' => 'Authorization',
                    'fields' => [
                        'username' => 'id'
                    ],
                    'parameter' => 'token',

                    // Boolean indicating whether the "sub" claim of JWT payload
                    // should be used to query the Users model and get user info.
                    // If set to `false` JWT's payload is directly returned.
                    'queryDatasource' => true,
                ]
            ],

            'unauthorizedRedirect' => false,
            'checkAuthIn' => 'Controller.initialize'

            // If you don't have a login action in your application set
            // 'loginAction' to false to prevent getting a MissingRouteException.
//            'loginAction' => false

        ]);
//        $this->RequestHandler->renderAs($this, 'json');
    }
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        if (!empty($this->Auth->user())) {
            Log::info('dddd');

        }
    }
    public function beforeRender(Event $event)
    {
        $paging = $this->request->getParam('paging');
        if ($paging !== false &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $pagingArr = current($paging);
            $this->set([
                'hasNextPage' => $pagingArr['nextPage'],
                'currentPage' => $pagingArr['page']
            ]);
        }
    }
    protected function renderResponse($statusCode = 404, $data = [])
    {
        $response = $this->response->withType('application/json')
            ->withStringBody(json_encode($data));
        $response->withStatus($statusCode);
        return $response;
    }
    public function isAuthorized($user)
    {
        Log::info('ddkdk');
        // Admin can access every action
        if (isset($user['roleId']) && $user['roleId'] === 1) {
            return true;
        }

        // Default deny
        return false;
    }
}
