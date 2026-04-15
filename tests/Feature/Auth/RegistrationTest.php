<?php

test('registration route is removed (get)', function () {
    $this->get('/register')->assertStatus(404);
});

test('registration route is removed (post)', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertStatus(404);
});
