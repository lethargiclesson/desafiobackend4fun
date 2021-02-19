<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * endpoint testing
 * 
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_post_user_registration()
    {
        $randomNumber = $this->faker->boolean(50);
        $randomType =  $randomNumber ? 'usuario' : 'lojista';
        $cnpj =  intval($this->faker->randomNumber(7, true) . $this->faker->randomNumber(7, true));

        if ($randomNumber === false) {
            $this->postJson('api/register', [
                'nome' => $this->faker->firstName() . $this->faker->firstName(),
                'password' => Hash::make('password'),
                'email' => $this->faker->safeEmail(),
                'cpf' => intval($this->faker->randomNumber(5) . $this->faker->randomNumber(6)),
                'tipo' => $randomType,
                'cnpj' => $cnpj,
            ])->assertStatus(201)->assertExactJson(['usuario' => 'Cadastro realizado com sucesso!']);
        } else if ($randomNumber === true) {
            $this->postJson('api/register', [
                'nome' => $this->faker->firstName() . $this->faker->firstName(),
                'password' => Hash::make('password'),
                'email' => $this->faker->safeEmail(),
                'cpf' => intval($this->faker->randomNumber(5) . $this->faker->randomNumber(6)),
                'tipo' => $randomType,
            ])->assertStatus(201)->assertExactJson(['usuario' => 'Cadastro realizado com sucesso!']);
        }
    }

    public function test_post_user_registration_password_too_short()
    {
        $this->postJson('api/register', [
            'nome' => $this->faker->name(),
            'password' => 11111,
            'email' => $this->faker->email(),
            'cpf' => 11122233344,
            'tipo' => 'usuario',
        ])->assertStatus(400)->assertJson(['error' => ['password' => ['The password must be at least 8 characters.']]]);
    }

    public function test_post_user_registration_cpf_too_short()
    {
        $this->postJson('api/register', [
            'nome' => $this->faker->name(),
            'password' => 11112222,
            'email' => $this->faker->email(),
            'cpf' => 1111222233,
            'tipo' => 'usuario',
        ])->assertStatus(400)->assertJson(['error' => ['cpf' => ['The cpf must be at least 11 characters.']]]);
    }

    public function test_post_user_registration_failure_cnpj()
    {
        $this->postJson('api/register', [
            'nome' => $this->faker->name(),
            'password' => Hash::make('password'),
            'email' => $this->faker->email(),
            'cpf' => doubleval($this->faker->randomNumber(5) . $this->faker->randomNumber(6)),
            'tipo' => 'lojista',
        ])->assertStatus(400)->assertJson(['error' => ['cnpj' => ['The cnpj field is required unless tipo is in usuario.']]]);
    }

    public function test_post_user_registration_cnpj_too_short()
    {
        $this->postJson('api/register', [
            'nome' => $this->faker->name(),
            'password' => Hash::make('password'),
            'email' => $this->faker->email(),
            'cpf' => doubleval($this->faker->randomNumber(5) . $this->faker->randomNumber(6)),
            'tipo' => 'lojista',
            'cnpj' => 1111222233334,
        ])->assertStatus(400)->assertJson(['error' => ['cnpj' => ['The cnpj must be at least 14 characters.']]]);
    }
}
