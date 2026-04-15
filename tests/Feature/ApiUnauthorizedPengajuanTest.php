<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

it('returns 401 json when accessing pengajuan list without token', function () {
    $response = $this->get('/api/pengajuan');

    // Jika masih redirect (302) ke /login berarti middleware belum bekerja
    if ($response->getStatusCode() === 302) {
        $target = $response->headers->get('Location');
        // Follow redirect secara manual untuk diagnosa
        $login = $this->followingRedirects()->get('/api/pengajuan');
        dump('Redirected to: '.$target);
        dump($login->getContent());
    }

    // Gunakan expectsJson agar Accept dipaksa
    $jsonResponse = $this->getJson('/api/pengajuan');

    $jsonResponse->assertStatus(401);
    $jsonResponse->assertJsonStructure(['status','message']);
    $jsonResponse->assertJson(['status' => false]);
});

