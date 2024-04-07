<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testLogin()
    {
        $user = User::query()->first();
        $data = [
            "phone_number" => $user->phone_number,
        ];
        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(422);
        $data = [
            "phone_number" => "09381412419",
            "password" => "password",
        ];
        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(200);
    }

//    public function testRegister()
//    {
//        $phoneNumber = "09381412411";
//        $data = [
//            "phone_number" => $phoneNumber,
//        ];
//        $response = $this->postJson('/api/auth/register', $data);
//        $response->assertStatus(201);
//        $response = $this->postJson('/api/auth/register', $data);
//        $response->assertStatus(403);
//    }

    public function testCheckOtpCode()
    {
        $phoneNumber = "09381412412";
        $data = [
            "phone_number" => $phoneNumber,
        ];
        $response = $this->postJson('/api/auth/register', $data);
        $response->assertStatus(201);
        $otp = Otp::query()->where($data)->first();
        $data['code'] = $otp->code;
        $response = $this->postJson('/api/auth/register/otp', $data);
        $response->assertStatus(200);
    }
}
