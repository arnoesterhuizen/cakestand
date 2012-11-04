<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    protected $_packet = array(
        'meta' => array(
            'modelClass'  => '', //required, the class name of the model
            'modelKey'    => '', //required, the primary key of the model
            'actions' => array(),
            'primaryKey' => '',
            'displayField' => '',
            'singularVar' => '',
            'pluralVar'   => '',
            'singularHumanName' => '',
            'pluralHumanName' => '',
            'scaffoldFields' => array(),
            'associations' => array(),
        ),
        'data' => array()
    );

    /**
     * @param array $_actions a list of actions that can be taken on this type of entity
     * - divided into specific (an entity ID is required) and generic
     * - and with a bitmask value indicating ACL permissions required
     *
     * In the beforeFilter, this parameter is merged with it's parent permissions
     */
    protected static $_actions = array(
        'generic' => array(
            'index' => 1,
            'add'   => 1
        ),
        'specific' => array(
            'view'   => 1,
            'edit'   => 1,
            'copy'   => 1,
            'delete' => 1
        )
    );

/**
 * sets up basic variables and configuration
 *
 * @return void
 */
    public function beforeFilter () {
        parent::beforeFilter();

        //populate packet['meta'] section
        $this->_packet['meta']['modelClass']        = $this->modelClass;
        $this->_packet['meta']['modelKey']          = $this->modelKey;

        if (!is_object($this->{$this->_packet['meta']['modelClass']})) {
            throw new MissingModelException($this->_packet['meta']['modelClass']);
        }

        $this->_packet['meta']['primaryKey']        = $this->{$this->modelClass}->primaryKey;
        $this->_packet['meta']['displayField']      = $this->{$this->modelClass}->displayField;
        $this->_packet['meta']['singularVar']       = Inflector::variable($this->modelClass);
        $this->_packet['meta']['pluralVar']         = Inflector::variable($this->name);
        $this->_packet['meta']['singularHumanName'] = Inflector::humanize(Inflector::underscore($this->modelClass));
        $this->_packet['meta']['pluralHumanName']   = Inflector::humanize(Inflector::underscore($this->name));
        $this->_packet['meta']['scaffoldFields']    = array_keys($this->{$this->modelClass}->schema());
        $this->_packet['meta']['associations']      = $this->_associations();

        if (isset(parent::$_actions)) {
            $this->_packet['meta']['actions']       = parent::$_actions;
        }
    }

/**
 * sets the default variables
 *
 * @return void
 */
    public function beforeRender () {
        parent::beforeRender();

        $this->set('data', $this->_packet['data']);
        $this->set('meta', $this->_packet['meta']);

        if (isset($this->request->query['debug'])) {
            debug($this->request);
            debug($this->response);
            debug($this->_packet);
        }
    }

/**
 * index method
 *
 * @return void
 */
    public function index() {
        $this->_packet['data'] = $this->paginate();
    }

/**
 * view method
 *
 * @return void
 */
    public function view() {
        $this->request->params['pass'][0] = explode(',', $this->request->params['pass'][0]);
        foreach ($this->request->params['pass'][0] as $id) {
            try {
                $this->_initEntity($id);
            }
            catch (NotFoundException $e) {
                $this->Session->setFlash($e->getMessage(), 'FlashMessage' . DS . 'warning');
            }
        }

        $findParams = array(
            'conditions' => array(
                $this->_packet['meta']['modelClass'] . '.' . $this->_packet['meta']['primaryKey'] => $this->request->params['pass'][0]
            )
        );

        $this->_packet['data'] = $this->{$this->modelClass}->find('all', $findParams);
        $this->render(DS . 'Elements' . DS . 'Generic' . DS . 'view');
    }

/**
 * add method
 *
 * @return void
 */
    public function add() {
        $this->_form(false);
    }

/**
 * search method
 *
 * @return void
 */
    public function search() {
        $this->_form(false);
    }

/**
 * edit method
 *
 * @return void
 */
    public function edit($id = null) {
        $this->_form($id);
    }

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
    public function delete($id = null) {
        $this->_initEntity($id);

        if ($this->_isMethodIn(array('post', 'delete'))) {
            if ($this->{$this->modelClass}->delete()) {
                $this->Session->setFlash(__('%s deleted', $this->_packet['meta']['singularHumanName']));
                $this->redirect(array('action' => 'index'));
            }

            $this->Session->setFlash(__('Unable to delete %s', $this->_packet['meta']['singularHumanName']));
        }
    }

/**
 * copy method
 *
 * @param string $id of entity to copy
 * @return void
 */
    public function copy () {
        $this->_initEntity($id);
        $data = $this->{$this->modelClass}->read(null, $this->params->pass[0]);
        unset($data[$this->modelClass]['id']);
        $this->{$this->modelClass}->save($data);
        $this->redirect(array('action' => 'index'));
    }

/**
 *
 *
 * @throws NotFoundException when the entity does not exist
 * @param string $id
 * @return void
 */
    protected function _form($id = null) {
        if (false !== $id) {
            $this->_initEntity($id);
        }

        if ($this->_isMethodIn(array('post', 'put'))) {
            $this->_save();
        }

        if (false !== $id) {
            $this->request->data = $this->{$this->modelClass}->read(null, $id);
        }
    }

/**
 * Tries to save the data in the request object and sets a status message. Also redirects on success.
 *
 */
    protected function _save() {
        //initialise the object if it is a new entity
        if (!isset($this->{$this->modelClass}->id)) {
            $this->{$this->modelClass}->create();
        }

        if ($this->{$this->modelClass}->saveAll($this->request->data)) {
            $this->Session->setFlash(__('%s saved', $this->_packet['meta']['singularHumanName']));
            $this->redirect(array('action' => 'view'));
        }

        $this->Session->setFlash(__('Unable to save %s', $this->_packet['meta']['singularHumanName']));
    }

/**
 * sets entity id and checks that it is a valid entity
 *
 * @throws NotFoundException when the entity does not exist
 * @param string $id
 * @return void
 */
    protected function _initEntity($id = null) {
        $this->{$this->modelClass}->id = $id;

        if (!$this->{$this->modelClass}->exists()) {
            throw new NotFoundException(
                __(
                    'Invalid %s: %s',
                    strtolower(
                        $this->_packet['meta']['singularHumanName']
                    ),
                    $id
                )
            );
        }
    }

/**
 * Checks that the method used to process this request is allowed
 *
 * @throws MethodNotAllowedException when an invalid method is used
 * @param array $methods allowed methods to access
 * @return void
 *
 * The difference between _checkMethodIn and _isMethodIn
 * - _checkMethodIn throws an exception if this fails
 * - _isMethodIn    only returns true or false
 */
    protected function _checkMethodIn($methods = array()) {
        if ($this->_isMethodIn($methods)) {
            return;
        }

        throw new MethodNotAllowedException();
    }

/**
 * Checks whether the method used to process this request is allowed
 *
 * @param array $methods methods to test against
 * @return boolean true on valid
 *
 * The difference between _checkMethodIn and _isMethodIn
 * - _checkMethodIn throws an exception if this fails
 * - _isMethodIn    only returns true or false
 */
    protected function _isMethodIn($methods = array()) {
        if (in_array($this->request->method(), $methods)) {
            return true;
        }

        return false;
    }

/**
 * Returns associations for controllers models.
 *
 * @return array Associations for model
 * @todo This belongs in the model? (arno.esterhuizen 2012-07-30)
 */
    protected function _associations() {
        $keys = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
        $associations = array();

        foreach ($keys as $key => $type) {
            foreach ($this->{$this->modelClass}->{$type} as $assocKey => $assocData) {
                $associations[$type][$assocKey]['primaryKey'] =
                    $this->{$this->modelClass}->{$assocKey}->primaryKey;

                $associations[$type][$assocKey]['displayField'] =
                    $this->{$this->modelClass}->{$assocKey}->displayField;

                $associations[$type][$assocKey]['foreignKey'] =
                    $assocData['foreignKey'];

                $associations[$type][$assocKey]['controller'] =
                    Inflector::pluralize(Inflector::underscore($assocData['className']));

                if ($type == 'hasAndBelongsToMany') {
                    $associations[$type][$assocKey]['with'] = $assocData['with'];
                }
            }
        }

        return $associations;
    }
}
