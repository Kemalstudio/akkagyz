<?php

test('public interface is translated to English', function () {
    $this->withSession(['locale' => 'en'])
        ->get('/')
        ->assertOk()
        ->assertSee('Delivery throughout Turkmenistan');
});

test('public interface is translated to Turkmen', function () {
    $this->withSession(['locale' => 'tk'])
        ->get('/')
        ->assertOk()
        ->assertSee('Türkmenistanyň ähli ýerine eltip bermek');
});

test('marketplace interface follows selected language', function () {
    $this->withSession(['locale' => 'en'])
        ->get('/marketplace')
        ->assertOk()
        ->assertSee('Seller marketplace');
});
