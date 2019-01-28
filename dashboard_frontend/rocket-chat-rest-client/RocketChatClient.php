<?php

namespace RocketChat;

include 'httpful/src/Httpful/Request.php';

use Httpful\Request;

class Client {

    public $api;

    function __construct() {
        $this->api = ROCKET_CHAT_INSTANCE . REST_API_ROOT;
        // set template request to send and expect JSON
        $tmp = Request::init()
                ->sendsJson()
                ->expectsJson();
        Request::ini($tmp);
    }

    /**
     * Get version information. This simple method requires no authentication.
     */
    public function version() {
        $response = \Httpful\Request::get($this->api . 'info')->send();
        return $response->body->info->version;
    }

    /**
     * Quick information about the authenticated user.
     */
    public function me() {
        $response = Request::get($this->api . 'me')->send();
        if ($response->body->status != 'error') {
            if (isset($response->body->success) && $response->body->success == true) {
                return $response->body;
            }
        } else {
            echo( $response->body->message . "\n" );
            return false;
        }
    }

    /**
     * List all of the users and their information.
     *
     * Gets all of the users in the system and their information, the result is
     * only limited to what the callee has access to view.
     */
    public function list_users() {
        $response = Request::get($this->api . 'users.list')->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return $response->body->users;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    public function list_users1() {
        $response = Request::get($this->api . 'users.list?query={ "name": { "$regex": "ipsaro" } }')->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return $response->body->users;
        } else {
            echo( $response->body->error . "\n" );
            return $response->body->error;
        }
    }

    public function find_users_channel($id) {
        $response = Request::get($this->api . 'channels.members')
                ->body(array('roomId' => $id))
                ->send();
        var_dump($response);
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return $response->body->users;
        } else {

            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * List the private groups the caller is part of.
     */
    public function list_groups() {
        $response = Request::get($this->api . 'groups.list')->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            $groups = array();
            foreach ($response->body->groups as $group) {
                $groups[] = new Group($group);
            }
            return $groups;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * List the channels the caller has access to.
     */
    public function list_channels() {
        $response = Request::get($this->api . 'channels.list')->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            $groups = array();
            foreach ($response->body->channels as $group) {
                $groups[] = new Channel($group);
            }
            return $groups;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    public function list_channelsIdName() {
        $response = Request::get($this->api . 'channels.list')->send();
        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            $groups = array();
            foreach ($response->body->channels as $group) {
                $groups[] = new Channel($group);
            }
            return $groups;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }
    
    public function logout() {
		// get user ID if needed
		$response = Request::post( $this->api . 'logout' )
		->send();
		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			//return true;
		} else {
			//echo( $response->body->error . "\n" );
			//return false;
		}
	}

}
