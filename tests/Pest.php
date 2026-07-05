<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests boot the full app and get a fresh in-memory SQLite database
| each test (RefreshDatabase) — so they never touch the real MySQL `planora`.
| Unit tests are lightweight and don't hit the database.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class)->in('Unit');
