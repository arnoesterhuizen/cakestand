<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
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
}
