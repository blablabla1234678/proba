<?php

namespace Tests\Feature;

class Data {
    public $user1 = [
        'name' => 'The Tester',
        'email' => 'test@example.com',
        'password' => 'abcd12345'
    ];
    public $user1_public;
    public $user1b = [
        'name' => 'The TesterB',
        'email' => 'testB@example.com',
        'password' => 'abcd1234578'
    ];
    public $user1b_public;
    public $user2 = [
        'name' => 'The Other Tester',
        'email' => 'test2@example.com',
        'password' => 'ABCD12345'
    ];
    public $user2_public;

    public $post1 = [
        'title' => 'The Post',
        'body' => 'This the the post body. It should contain a long text.'
    ];
    public $post1b = [
        'title' => 'The Post2',
        'body' => 'This the the post body2. It should contain a long text.'
    ];
    public $post2 = [
        'title' => 'The Other Post',
        'body' => 'This the the other post body. It should contain a long text.'
    ];

    public function __construct()
    {
        $this->user1_public = $this->maskPrivateUserData($this->user1);
        $this->user1b_public = $this->maskPrivateUserData($this->user1b);
        $this->user2_public = $this->maskPrivateUserData($this->user2);
    }

    protected function maskPrivateUserData(array $data):array {
        return [
            'name' => $data['name'],
            'email' => $data['email']
        ];
    }
}