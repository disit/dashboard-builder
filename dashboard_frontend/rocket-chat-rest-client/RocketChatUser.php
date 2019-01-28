<?php

namespace RocketChat;

use Httpful\Request;
use RocketChat\Client;

include '../config.php';

class User extends Client {

    public $username;
    private $password;
    public $id;
    public $nickname;
    public $email;

    public function __construct($fields = array()) {
        include '../config.php';
        parent::__construct();
        $this->username = $userIdAdminChat.'UserAdmin';
        $this->password = $passAdminChat.'4$GMEuu6!!5$ks2EX^!bNWM%5kiJfAmQ@n';
        if (isset($fields['nickname'])) {
            $this->nickname = $fields['nickname'];
        }
        if (isset($fields['email'])) {
            $this->email = $fields['email'];
        }
    }

    /**
     * Authenticate with the REST API.
     */
    public function login($save_auth = true) {
        $response = Request::post($this->api . 'login')
                ->body(array('user' => $this->username, 'password' => $this->password))
                ->send();
        if ($response->code == 200 && isset($response->body->status) && $response->body->status == 'success') {
            if ($save_auth) {
                // save auth token for future requests
                $tmp = Request::init()
                        ->addHeader('X-Auth-Token', $response->body->data->authToken)
                        ->addHeader('X-User-Id', $response->body->data->userId);
                Request::ini($tmp);
            }
            $this->id = $response->body->data->userId;
            return true;
        } else {
            //echo( $response->body->message . "\n" );
            return false;
        }
    }

    /**
     * Gets a user’s information, limited to the caller’s permissions.
     */
    public function info() {
        $response = Request::get($this->api . 'users.info?userId=' . $this->id)->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            $this->id = $response->body->user->_id;
            $this->nickname = $response->body->user->name;
            $this->email = $response->body->user->emails[0]->address;
            return $response->body;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * Gets a user’s information, limited to the caller’s permissions.
     */
    public function infoByUsername($username) {
        $response = Request::get($this->api . 'users.info?username=' . $username)->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            $this->id = $response->body->user->_id;
            $this->nickname = $response->body->user->name;
            $this->email = $response->body->user->emails[0]->address;
            return $response->body;
        } else {
            //echo( $response->body->error . "\n" );
            return $response->body;
        }
    }

    /**
     * Create a new user.
     */
    public function create() {
        $response = Request::post($this->api . 'users.create')
                ->body(array(
                    'name' => $this->nickname,
                    'email' => $this->email,
                    'username' => $this->username,
                    'password' => $this->password,
                ))
                ->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            $this->id = $response->body->user->_id;
            return $response->body->user;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * Deletes an existing user.
     */
    public function delete() {
        // get user ID if needed
        if (!isset($this->id)) {
            $this->me();
        }
        $response = Request::post($this->api . 'users.delete')
                ->body(array('userId' => $this->id))
                ->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return true;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    public function deleteToken($token) {
        
        $response = Request::post($this->api . 'users.removePersonalAccessToken')
                ->body(array('tokenName' => $token))
                ->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return true;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }
    
    public function setRole($userId) {
        
        $response = Request::post($this->api . 'users.update')
                ->body(array('userId'=>$userId, 'data'=> ['roles' => [ 'rootAdminDisit' ]]))
                ->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return true;
        } else {
            echo( $response . "\n" );
            return false;
        }
    }

}
