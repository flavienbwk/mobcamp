<?php

use App\Publication;

class PublicationsApiTest extends TestCase {

    public function setUp() {
        parent::setUp();
        Illuminate\Support\Facades\Artisan::call('migrate');
    }

    public function testGetPublications() {
        $post = factory(\App\Publication::class)->create();
        $response = $this->call("GET", "/publications", ['id' => $post->id]);
        $publications = json_decode($response->getContent());
        
        $this->assertsEquals(200, $response->getStatusCode());
        $this->assertsEquals(2, count($comments));
    }

}
