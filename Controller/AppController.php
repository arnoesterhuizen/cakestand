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
		'meta' => array(),
		'data' => array()
	);

/**
 * sets up basic variables and configuration
 *
 * @return void
 */
	public function beforeFilter () {
		parent::beforeFilter();

		//populate packet['meta'] section
		$modelMeta = array(
			'modelClass' => $this->modelClass,
			'modelKey'   => $this->modelKey
		);

		if (!is_object($this->{$modelMeta['modelClass']})) {
			throw new MissingModelException($modelMeta['modelClass']);
		}

		$modelExtra = array(
			'primaryKey' => $this->{$this->modelClass}->primaryKey,
			'displayField' => $this->{$this->modelClass}->displayField,
			'singularVar' => Inflector::variable($this->modelClass),
			'pluralVar' => Inflector::variable($this->name),
			'singularHumanName' => Inflector::humanize(Inflector::underscore($this->modelClass)),
			'pluralHumanName' => Inflector::humanize(Inflector::underscore($this->name)),
			'scaffoldFields' => array_keys($this->{$this->modelClass}->schema()),
			'associations' => $this->_associations()
		);

		$this->_packet['meta'] = array_merge($modelMeta, $modelExtra);
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
		$this->_initEntity();
		$this->_packet['data'] = $this->{$this->modelClass}->read(null, $this->params->pass[0]);
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		$this->_initEntity();
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->{$this->modelClass}->saveAll($this->request->data)) {
				$this->Session->setFlash(__('%s saved', $this->_packet['meta']['singularHumanName']));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Unable to save %s', $this->_packet['meta']['singularHumanName']));
			}
		} else {
			$this->request->data = $this->{$this->modelClass}->read(null, $this->params->pass[0]);
		}
	}

/**
 * copy method
 *
 * @param string $id of entity to copy
 * @return void
 */
	public function copy () {
		$this->_initEntity();
		$data = $this->{$this->modelClass}->read(null, $this->params->pass[0]);
		unset($data[$this->modelClass]['id']);
		$this->{$this->modelClass}->save($data);
		$this->redirect(array('action' => 'index'));
	}

/**
 * sets entity id and checks that it is a valid entity
 *
 * @throws NotFoundException when the entity does not exist
 * @param string $id
 * @return void
 */
	protected function _initEntity($id = null) {
		if (null == $id) {
			$id = $this->params->pass[0];
		}

		$this->{$this->modelClass}->id = $id;
		if (!$this->{$this->modelClass}->exists()) {
			throw new NotFoundException(__('Invalid %s', strtolower(Inflector::humanize(Inflector::underscore($this->modelClass)))));
		}
	}

/**
 * Returns associations for controllers models.
 *
 * @return array Associations for model
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
