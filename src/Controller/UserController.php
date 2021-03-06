<?php
namespace App\Controller;

use App\Controller\AppController;
use \Firebase\JWT\JWT;
use Cake\Log\Log;

/**
 * User Controller
 *
 * @property \App\Model\Table\UserTable $User
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserController extends AppController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->Auth->allow(['login', 'add']);
    }

    public $paginate = [
        'fields' => ['User.id', 'User.name'],
        'limit' => 5,
        'order' => [
            'User.name' => 'asc'
        ]
    ];

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
//                $this->Auth->setUser($user);
                $key = "example_key";
                $token = array(
                    "id" => $user['id']
                );
                $jwt = array('token' => JWT::encode($token, $key));
                return $this->renderResponse(200, array_merge($user, $jwt));
            } else {
                return $this->renderResponse(403, ['message' => 'Data not found']);
            }
        }
    }
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {

//        Log::debug($this->request->getHeaderLine('Authorization'));
        $user = $this->paginate($this->User);

        return $this->renderResponse(200, $user);
        /*$this->set([
            'data' => $user,
            'status' => 1,
            '_serialize' => ['data', 'status', 'hasNextPage', 'currentPage']
        ]);*/
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

        try {
            $user = $this->User->get($id, [
                'contain' => []
            ]);
            /*$this->set([
                'data' =>$user,
                '_serialize' => ['data']
            ]);*/
            return $this->renderResponse(200, $user);
        } catch (\Exception $e) {
            $this->response->statusCode(403);
            /*$this->set([
                'status' => 0,
                'message' => 'Data not found',
                '_serialize' => ['status', 'message']
            ]);*/
            return $this->renderResponse(403, ['message' => 'Data not found']);
        }

    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $res = array();
        $user = $this->User->newEntity();
        if ($this->request->is('post')) {
            $user = $this->User->patchEntity($user, $this->request->getData());
//            print($user);
            if ($this->User->save($user)) {
                $res['status'] = 1;
                $res['message'] = 'The user has been saved.';
            } else {
                $res['status'] = 0;
                $res['message'] = 'The user could not be saved. Please, try again.';
            }
        }
        $this->set([
            'data' => $user,
            'res' => $res,
            '_serialize' => ['data', 'res']
        ]);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $res = array();
        $user = $this->User->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->User->patchEntity($user, $this->request->getData());
            if ($this->User->save($user)) {
                $res['status'] = 1;
                $res['message'] = 'The user has been saved.';
            } else {
                $res['status'] = 0;
                $res['message'] = 'The user could not be saved. Please, try again.';
            }
        }
        $this->set([
            'data' => $user,
            'res' => $res,
            '_serialize' => ['data', 'res']
        ]);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $res = array();
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->User->get($id);
        if ($this->User->delete($user)) {
            $res['status'] = 1;
            $res['message'] = 'The user has been deleted.';
        } else {
            $res['status'] = 0;
            $res['message'] = 'The user could not be deleted. Please, try again.';
        }

        $this->set([
            'res' => $res,
            '_serialize' => ['data', 'res']
        ]);

    }
}
